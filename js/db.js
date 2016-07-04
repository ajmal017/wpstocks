
// Check for database support
    var indexedDB = window.indexedDB || window.webkitIndexedDB
    || window.mozIndexedDB || window.msIndexedDB || false,
        IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange
    || window.mozIDBKeyRange || window.msIDBKeyRange || false,
        webSQLSupport = ('openDatabase' in window);

// open database, create object store and provide Web SQL fallback
    var db; // the db to store the database connection
    var openDB = function() {
      if(indexedDB) {
// the open method is aynchronous; while the request is in progress, open() immediately returns an IDBRequest. If not database exists, create one, and then create a connection to the database.
        var request = indexedDB.open('tasks', 1),
// if upgradeneeded is a member of the request object the browser supports upgradeNeeded event
            upgradeNeeded = ('onupgradeneeded' in request);
        request.onsuccess = function(e) {
          db = e.target.result;
// if the event upgradeNeeded doesn't exist, then the browser supports the deprecated setVersion method
          if(!upgradeNeeded && db.version != '1') {
// if the version is not equal to 1 then no object store exists and it must be created. Object stores can only be created during a version-change transaction. So increase the version number of the current database by calling db.setVersion with a version argument set to 1
            var setVersionRequest = db.setVersion('1');
            setVersionRequest.onsuccess = function(e) {
              var objectStore = db.createObjectStore('tasks', {
                keyPath: 'id'
              });
// use createIndex() to create another index for the objectStore. This index will be used later.
              objectStore.createIndex('desc', 'descUpper', {
                unique: false
              });
              loadTasks();
            }
          } else {
            loadTasks();
          }
        }
        if(upgradeNeeded) {
	    // this event handler will called when the database is created for the first time
          request.onupgradeneeded = function(e) {
            db = e.target.result;
            var objectStore = db.createObjectStore('tasks', {
              keyPath: 'id'
            });
            objectStore.createIndex('desc', 'descUpper', {
              unique: false
            });
          }
        }
      } else if(webSQLSupport) {
	  // Allocate 5 MB for the database
        db = openDatabase('tasks','1.0','Tasks database',(5*1024*1024));
        db.transaction(function(tx) {
          var sql = 'CREATE TABLE IF NOT EXISTS tasks ('+
              'id INTEGER PRIMARY KEY ASC,'+
              'desc TEXT,'+
              'due DATETIME,'+
              'complete BOOLEAN'+
              ')';
// Use the execuseSql() method of the transaction object to create a tasks table if it doesn't already exist.[] means no optional argument array  being passed. loadTasks() is the callback function
          tx.executeSql(sql, [], loadTasks);
        });
      }
    }
    openDB();





    var createEmptyItem = function(query, taskList) {
      var emptyItem = document.createElement('li');
	// if a query doesn't exist the search will return zero results
      if(query.length > 0) {
        emptyItem.innerHTML = '<div class="item_title">'+
          'No tasks match your query <strong>'+query+'</strong>.'+
          '</div>';
      } else {
        emptyItem.innerHTML = '<div class="item_title">'+
          'No tasks to display. <a href="#add">Add one</a>?'+
          '</div>';
      }
      taskList.appendChild(emptyItem);
    }

// the showTask() function creates and displays a task list item containing a title, due date, checkbox, and Delete button.
    var showTask = function(task, list) {
      var newItem = document.createElement('li'),
          checked = (task.complete == 1) ? ' checked="checked"' : '';
      newItem.innerHTML =
        '<div class="item_complete">'+
        '<input type="checkbox" name="item_complete" '+
        'id="chk_'+task.id+'"'+checked+'>'+
        '</div>'+
        '<div class="item_delete">'+
        '<a href="#" id="del_'+task.id+'">Delete</a>'+
        '</div>'+
        '<div class="item_title">'+task.desc+'</div>'+
        '<div class="item_due">'+task.due+'</div>';
      list.appendChild(newItem);
// the markAsComplete() event handler is executed when the user marks or unmarks the checkbox
      var markAsComplete = function(e) {
        e.preventDefault();
        var updatedTask = {
          id: task.id,
          desc: task.desc,
          descUpper: task.desc.toUpperCase(),
          due: task.due,
          complete: e.target.checked
        };
        updateTask(updatedTask);
      }
// the remove() event handler is executed when the user clicks the Delete button for a task item
      var remove = function(e) {
        e.preventDefault();
        if(confirm('Deleting task. Are you sure?', 'Delete')) {
          deleteTask(task.id);
        }
      }
      document.getElementById('chk_'+task.id).onchange =
        markAsComplete; // this code attaches event handlers to the task item's check box and remove button
      document.getElementById('del_'+task.id).onclick = remove;
    }



    var loadTasks = function(q) {
      var taskList = document.getElementById('task_list'),
          query = q || '';
      taskList.innerHTML = '';
      if(indexedDB) {
        var tx = db.transaction(['tasks'], 'readonly'),
            objectStore = tx.objectStore('tasks'), cursor, i = 0;
        if(query.length > 0) {
          var index = objectStore.index('desc'),
              upperQ = query.toUpperCase(),
// build a key range on the uppercase version of the task description. The 'z' appended to the second argument allows the application to search for a task description beginning with the search term (otherwise it would only return exact matches)
              keyRange = IDBKeyRange.bound(upperQ, upperQ+'z');
          cursor = index.openCursor(keyRange);
        } else {
          cursor = objectStore.openCursor();
        }
        cursor.onsuccess = function(e) {
// e.target references the cursor so get the result set from the cursor.
          var result = e.target.result;
          if(result == null) return;
// count the number of tasks passed to showTask(). The resulting value will be used by the transaction event handler, tx.onComplete(), to determine if an empty task list should be rendered.
          i++;
          showTask(result.value, taskList);
// use result['continue'] to find the next matching task in the index or the next task in the object store (if not searching). 
          result['continue']();
        }
        tx.oncomplete = function(e) {
          if(i == 0) { createEmptyItem(query, taskList); }
        }
// if indexedDB is not supported and Web SQL is, build a query that will return that will retrieve the tasks from the database
      } else if(webSQLSupport) {
        db.transaction(function(tx) {
          var sql, args = [];
          if(query.length > 0) {
            sql = 'SELECT * FROM tasks WHERE desc LIKE ?';
            args[0] = query+'%';
          } else {
            sql = 'SELECT * FROM tasks';
          }
          var iterateRows = function(tx, results) {
            var i = 0, len = results.rows.length;
            for(;i<len;i++) {
              showTask(results.rows.item(i), taskList);
            }
            if(len === 0) { createEmptyItem(query, taskList); }
          }
          tx.executeSql(sql, args, iterateRows);
        });
      }
    }



    var searchTasks = function(e) {
      e.preventDefault();
      var query = document.forms.search.query.value;
// if a query was typed in, pass the query as an argument to the loadTasks function
      if(query.length > 0) {
        loadTasks(query);
      } else {
        loadTasks();
      }
    }
// when the user submits the search form, call the searchTasks() function
    document.forms.search.addEventListener('submit', searchTasks, false);



    var insertTask = function(e) {
      e.preventDefault();
      var desc = document.forms.add.desc.value,
          dueDate = document.forms.add.due_date.value;
      if(desc.length > 0 && dueDate.length > 0) {
// construct a task object to store in the database. The key is the id property which is in the current time, and you also store the uppercase version of the description in order to implement case-insensitive searching.
        var task = {
          id: new Date().getTime(),
          desc: desc,
          descUpper: desc.toUpperCase(),
          due: dueDate,
          complete: false
        }
        if(indexedDB) {
          var tx = db.transaction(['tasks'], 'readwrite');
          var objectStore = tx.objectStore('tasks');
// add the task to the object store using the indexedDB method add().
          var request = objectStore.add(task);
// When a task has been successfully added call the event handler updateView().
          tx.oncomplete = updateView;

        } else if(webSQLSupport) {
          db.transaction(function(tx) {
// for the Web SQL callback, use and INSERT statement to add the task
            var sql = 'INSERT INTO tasks(desc, due, complete) '+
                'VALUES(?, ?, ?)',
                args = [task.desc, task.due, task.complete];
            tx.executeSql(sql, args, updateView);
          });
        }
      } else {
        alert('Please fill out all fields', 'Add task error');
      }
    }
// updateView() loads tasks from the database 
    function updateView(){
      loadTasks();
      alert('Task added successfully', 'Task added');
      document.forms.add.desc.value = '';
      document.forms.add.due_date.value = '';
      location.hash = '#list';
    }
// add the event handler insertTask() to the Add Task form's submit button
    document.forms.add.addEventListener('submit', insertTask, false);




    var updateTask = function(task) {
      if(indexedDB) {
        var tx = db.transaction(['tasks'], 'readwrite');
        var objectStore = tx.objectStore('tasks');
// use the put() method, passing the task object as an argument, to update the task in the database. The task object must have the correct key value, or the database may create a new value in the store rather than update the existing item.
        var request = objectStore.put(task);
      } else if(webSQLSupport) {
        var complete = (task.complete) ? 1 : 0;
        db.transaction(function(tx) {
          var sql = 'UPDATE tasks SET complete = ? WHERE id = ?',
              args = [complete, task.id];
          tx.executeSql(sql, args);
        });
      }
    }
    var deleteTask = function(id) {
      if(indexedDB) {
        var tx = db.transaction(['tasks'], 'readwrite');
        var objectStore = tx.objectStore('tasks');
// use the delete method to remove a task.
        var request = objectStore['delete'](id);
// when the delete operation has successfully completed, load the Task List view to show updated items.
        tx.oncomplete = loadTasks;
      } else if(webSQLSupport) {
        db.transaction(function(tx) {
          var sql = 'DELETE FROM tasks WHERE id = ?',
              args = [id];
          tx.executeSql(sql, args, loadTasks);
        });
      }
    }



    var dropDatabase = function() {
      if(indexedDB) {
// use the deleteDatabase() method to drop the tasks database.
        var delDBRequest = indexedDB.deleteDatabase('tasks');
// reload the page to initate a load event. This will trigger the load() event handler to create a fresh copy of the database.
        delDBRequest.onsuccess = window.location.reload();
      } else if(webSQLSupport) {
        db.transaction(function(tx) {
// in your Web SQL callback, clear down the tasks table rather than drop the entire database.
          var sql = 'DELETE FROM tasks';
          tx.executeSql(sql, [], loadTasks);
        });
      }
    }


