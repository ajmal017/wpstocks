/*
Example usage:
var dbname = 'fbh';
var tables = [
    {
	'name':'friends',
	'keypath':null,
	'indexes':null
    }
];
var db = initLocalDB(dbname, tables, function(){
});
var friends = getRecords(db, dbname, table[0]);
var record = {'first_name':'me'};
insertRecord(db, dbname, table[0], record, function(){
    friends = getRecords(db, dbname, table[0]);
});
*/

function localDB(dbname, tables, records, callback){
    // open database, create object store and provide Web SQL fallback
    openDB(dbname, tables, records, callback); // the db to store the database connection
}

var checkDBSupport = function(){
    // Check for database support
    var indexedDB = window.indexedDB || window.webkitIndexedDB
	|| window.mozIndexedDB || window.msIndexedDB || false,
    IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange
	|| window.mozIDBKeyRange || window.msIDBKeyRange || false,
    webSQLSupport = ('openDatabase' in window);
    return indexedDB;
}

var loadTables = function(db, tables, records){
    console.log('Calling loadTables()');
    console.log(db);
    console.log(tables);
    var j = 0;
    for(j in tables){

	console.log(tables[j]['name']);
	try{
	    db.deleteObjectStore(tables[j]['name']);
	}
	catch(err){
	}
	objectStore = db.createObjectStore(tables[j]['name'], {
	    keyPath: tables[j]['keypath']?tables[j]['keypath']:'id' // id
	});
	// use createIndex() to create another index for the objectStore. This index will be used later.  Indexes are created with the objectStore createIndex function which can get three parameters – the name of the index, the name of the property to put the index on and an options object.
	// desc, descUpper, {unique:false}
	if(tables[j]['indexes']!=null){
	    var k = 0;
	    for(k in tables[j]['indexes']){
		objectStore.createIndex(tables[j]['indexes'][k]['name'], tables[j]['indexes'][k]['property'], tables[j]['indexes'][k]['options']);
	    }
	}

	console.log('records:');
	console.log(records[j]);
	if(records!=null){
	    for(var i in records[j]){
		console.log('adding ');
		console.log(JSON.stringify(records[j][i]));
		objectStore.add(records[j][i]);
	    }
	}

    }

    return db;
}

var dropDB = function(dbname){
    console.log('Calling dropDB()');
    var db = null;
    var indexedDB = checkDBSupport();
    if(indexedDB) {

            var delDBRequest = indexedDB.deleteDatabase(dbname);
            console.log("dropDB() life is good with indexedDB");
    }
}

var getDBRecords = function(dbname, tables, callback){
    console.log('Calling getDBRecords()');
    var indexedDB = checkDBSupport();
    if(indexedDB) {
        var request = indexedDB.open(dbname, 1);

	request.onerror = function(e){
            console.log("getDBRecords() Oppss... we got into problems and errors. e:" + e);
	};	


	request.onsuccess = function(e) {
	    console.log('Setting db to event.target.result');
            db = event.target.result;
	    console.log(db);
	    getRecords(db, dbname, tables, callback);
            console.log("life is good with indexedDB e:" + e) ;
	};

    }
}

var openDB = function(dbname, tables, records, callback) {
    console.log('Calling openDB()');
    var db = null;
    var indexedDB = checkDBSupport();
    if(indexedDB) {
	console.log('Got indexedDB');
	// the open() method is aynchronous; while the request is in progress, open() immediately returns an IDBRequest. If no database exists, create one, and then create a connection to the database.
        var request = indexedDB.open(dbname, 1);

	request.onerror = function(e){
            console.log("Oppss... we got into problems and errors. e:" + e);
	};	

	request.onupgradeneeded = function(event) {
            console.log("UPGRADE our nice example DB") ;
	    console.log('Setting db to event.target.result');
            db = event.target.result;
	    console.log(db);
	    db = loadTables(db, tables, records);
	    callback(db);
	};

	request.onsuccess = function(e) {
            console.log("life is good with indexedDB e:" + e) ;
	};


/*
	// if upgradeneeded is a member of the request object the browser supports upgradeNeeded event
        upgradeNeeded = ('onupgradeneeded' in request);
        request.onsuccess = function(e) {
	    console.log('Setting db to e.target.result');
            db = e.target.result;
	    console.log(db);
	    // if the event upgradeNeeded doesn't exist, then the browser supports the deprecated setVersion method
            if(!upgradeNeeded && db.version != '1') {
		// if the version is not equal to 1 then no object store exists and it must be created. Object stores can only be created during a version-change transaction. So increase the version number of the current database by calling db.setVersion with a version argument set to 1
//		var setVersionRequest = db.setVersion('1');
//		setVersionRequest.onsuccess = function(e) {
		request.onupgradeneeded = function(event) {
		    console.log("UPGRADE our nice example DB *") ;
		    db = loadTables(db, tables);
		    callback(db); //loadTasks();
		}
            } else {
		console.log('no upgrade');
		callback(db); //loadTasks();
            }
        }
        if(upgradeNeeded) {
	    // this event handler will called when the database is created for the first time
            request.onupgradeneeded = function(e) {
		console.log('Setting db to e.target.result (upgrade needed)');
		db = e.target.result;
	//	db = loadTables(db, tables);
		console.log('db (after calling loadTables():');
		console.log(db);
		callback(db);
            }
        }
*/
    } else if(webSQLSupport) {
	// Allocate 5 MB for the database
        db = openDatabase(dbname,'1.0',dbname+' database',(5*1024*1024));
	for(j in tables){
            db.transaction(function(tx) {
		var sql = tables[j]['create_sql'];
		// Use the executeSql() method of the transaction object to create a table if it doesn't already exist.[] means no optional argument array  being passed. loadTasks() is the callback function
		tx.executeSql(sql, [], callback(db)); // loadTasks
            });
	}

    }
    return db;
}



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



var getRecords = function(db, dbname, table, callback) {
    var i = 0;
    var records = [];
    var indexedDB = checkDBSupport();
    if(indexedDB) {
            var tx = db.transaction(table['name'], 'readonly'),
            objectStore = tx.objectStore(table['name']), cursor, i = 0;
	    cursor = objectStore.openCursor();
            cursor.onsuccess = function(e) {
		console.log('cursor.onsuccess()');
		// e.target references the cursor so get the result set from the cursor.
		var result = e.target.result;
		console.log('result');
		console.log(result);
		if(result == null) {
			callback(records);
			return;
		}
		else{
		    records[i] = result['value'];
		    i++;
		    console.log(records);
		    // use result['continue'] to find the next record in the object store (if not searching). 
		    result['continue']();
		}
            }
            tx.oncomplete = function(e) {
            }

	// if indexedDB is not supported and Web SQL is, build a query that will return that will retrieve the tasks from the database
    } else if(webSQLSupport) {
        db.transaction(function(tx) {
	    var sql, args = [];
	    sql = 'SELECT * FROM '+table;
	    var iterateRows = function(tx, results) {
		var i = 0, len = results.rows.length;
		for(;i<len;i++) {
		    records[i] = results.rows.item(i);
		}
		if(len === 0) {
		//    createEmptyItem(query, taskList); 
		}
	    }
	    tx.executeSql(sql, args, iterateRows);
        });
    }
    return records;
}

var insertRecord = function(db, dbname, table, record, callback) {
    console.log('insertRecord()');
    var indexedDB = checkDBSupport();
    if(indexedDB) {

	console.log('db:');
	console.log(db);
	console.log('table:');
	console.log(table['name']);

	/*(
	  var objectStore = db.createObjectStore("friends", { keyPath: "id" });
	  objectStore.createIndex("id", "id", { unique: true });
	  objectStore.createIndex("name", "name", { unique: false });
	  objectStore.createIndex("first_name", "first_name", { unique: false });
	  objectStore.createIndex("last_name", "last_name", { unique: false });
	*/

	// open a read/write db transaction, ready for adding the data
	// var tx = db.transaction(storename, mode);
	// return tx.objectStore(storename);
	var transaction = db.transaction(table['name'], "readwrite");
	
	// report on the success of opening the transaction
	transaction.oncomplete = function(event) {
	    console.log('record saved');
	};

	transaction.onerror = function(event) {
	    console.log('error saving record');
	};

	// create an object store on the transaction
	var objectStore = transaction.objectStore(table['name']);
	// add our newItem object to the object store
	console.log('adding record');
	var request = objectStore.add(record);





	//	var tx = db.transaction([dbname], 'readwrite');
	/*
	  var tx = db.transaction([table['name']], 'readwrite');
	  var objectStore = tx.objectStore(table['name']);
	  // add the record to the object store using the indexedDB method add().
	  var request = objectStore.add(record);
	  // When a record has been successfully added call the event handler callback.
	  tx.oncomplete = callback;
	*/

    } else if(webSQLSupport) {
	db.transaction(function(tx) {
	    // for the Web SQL callback, use and INSERT statement to add the task
	    var sql = table['insert_sql'];
	    tx.executeSql(sql, [], callback);
	});
    }
}

var updateRecord = function(db, dbname, table, record, callback) {
    var indexedDB = checkDBSupport();
    if(indexedDB) {
        var tx = db.transaction([dbname], 'readwrite');
        var objectStore = tx.objectStore(table['name']);
	// use the put() method, passing the task object as an argument, to update the task in the database. The task object must have the correct key value, or the database may create a new value in the store rather than update the existing item.
        var request = objectStore.put(record);
    } else if(webSQLSupport) {
        db.transaction(function(tx) {
	    var sql = table['update_sql'];
	    tx.executeSql(sql, []);
        });
    }
}

var deleteRecord = function(db, dbname, table, record, id, callback) {
    var indexedDB = checkDBSupport();
    if(indexedDB) {
        var tx = db.transaction([dbname], 'readwrite');
        var objectStore = tx.objectStore(table['name']);
	// use the delete method to remove a record
        var request = objectStore['delete'](id);
        tx.oncomplete = callback();
    } else if(webSQLSupport) {
        db.transaction(function(tx) {
	    var sql = table['delete_sql'];
	    args = [id];
	    tx.executeSql(sql, args, callback());
        });
    }
}



var dropDatabase = function(dbname, db, tables, callback) {
    var indexedDB = checkDBSupport();
    if(indexedDB) {
	// use the deleteDatabase() method to drop the tasks database.
        var delDBRequest = indexedDB.deleteDatabase(dbname);
	// reload the page to initate a load event. This will trigger the load() event handler to create a fresh copy of the database.
        delDBRequest.onsuccess = window.location.reload();
    } else if(webSQLSupport) {
        db.transaction(function(tx) {
	    var i = 0;
	    var sql = null;
	    for(i in tables){
		var sql = 'DELETE FROM '+tables[i]['name'];
		tx.executeSql(sql, [], callback());
	    }
        });
    }
}


