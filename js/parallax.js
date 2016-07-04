
/*
<div class="pwt_parallax">
  <div class="pwt_parallax__layer pwt_parallax__layer--back">
    ...
  </div>
  <div class="pwt_parallax__layer pwt_parallax__layer--base">
    ...
  </div>
</div>
*/


/*
The parallax class is where the parallax magic happens. Defining the height and perspective style properties of an element will lock the perspective to its centre, creating a fixed origin 3D viewport. Setting overflow-y: auto will allow the content inside the element to scroll in the usual way, but now descendant elements will be rendered relative to the fixed perspective. This is the key to creating the parallax effect.

Next is the parallax__layer class. As the name suggests, it defines a layer of content to which the parallax effect will be applied; the element is pulled out of content flow and configured to fill the space of the container.

Finally we have the modifier classes parallax__layer--base and parallax__layer--back. These are used to determine the scrolling speed of a parallax element by translating it along the Z axis (moving it farther away, or closer to the viewport). For brevity I have only defined two layer speeds - we'll add more later.
*/
.pwt_parallax {
  perspective: 1px;
  height: 100vh;
  overflow-x: hidden;
  overflow-y: auto;
}
.pwt_parallax__layer {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.pwt_parallax__layer--base {
  transform: translateZ(0);
}
.pwt_parallax__layer--back {
  transform: translateZ(-1px);
}
