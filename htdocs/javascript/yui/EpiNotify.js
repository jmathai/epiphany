YAHOO.namespace('notify');
YAHOO.notify = function(params)
{
  //console.info(params.toSource());
  var params = arguments.length == 1 ? arguments[0] : {};
  var parentContainer = null;
  var container = null;
  var header = null;
  var containerId = 'EpiContainer';
  var defaultMessage = 'Message Center';
  var headerClass = 'EpiMessageClass';
  var headerId = 'EpiMessage';
  var fadeTime = 5000;

  return {
    init: function()
    {
      container = YAHOO.util.Dom.get(containerId);
      if(!container)
      {
        ////console.info(params.toSource());
        var cont = document.createElement('div');
        var target = document.body;
        if(params.parentContainer != null)
          target = YAHOO.util.Dom.get(params.parentContainer);
        cont.id = containerId;

        YAHOO.util.Dom.insertBefore(cont, YAHOO.util.Dom.getFirstChild(target));
        
        if(params.class != undefined)
          YAHOO.util.Dom.addClass(container, params.class);
        if(params.parentContainer != undefined)
          parentContainer = params.parentContainer;
        else
          parentContainer = containerId;
//console.info('parent container is ' + parentContainer);
        parentContainer = YAHOO.util.Dom.get(parentContainer);
        container = YAHOO.util.Dom.get(containerId);
      }

      if(header == null)
      {
        var hdr = document.createElement('div');
        hdr.id = headerId;

        if(params.message != undefined)
          hdr.innerHTML = params.message;
        else
          hdr.innerHTML = defaultMessage;
        
        els = YAHOO.util.Dom.getChildren(container);
        if(els.length == 0)
        {
          container.appendChild(hdr);
        }
        else
        {
          YAHOO.util.Dom.insertBefore(hdr, YAHOO.util.Dom.getFirstChild(container));
        }
        header = YAHOO.util.Dom.get(headerId);
      }

      YAHOO.util.Event.addListener(document, 'scroll', this.reposition);
      this.reposition();
    },

    reposition: function()
    {
      //console.info('scroll');
      var t = YAHOO.util.Dom.getDocumentScrollTop() + 'px';
      var w = parseInt(YAHOO.util.Dom.getStyle(parentContainer, 'width')) + 50;
      if(isNaN(w))
        w = 0;
      var l = (YAHOO.util.Dom.getDocumentScrollLeft()+YAHOO.util.Dom.getViewportWidth()-w) + 'px';
      /*//console.info('width : ' + w);
      //console.info('left : ' + l);
      //console.info('top : ' + t);*/
      YAHOO.util.Dom.setStyle(parentContainer, 'position', 'absolute');
      YAHOO.util.Dom.setStyle(parentContainer, 'top', t);
      YAHOO.util.Dom.setStyle(parentContainer, 'left', l);
//console.info('parent container is ' + parentContainer.toSource());
    },

    send: function(msg)
    {
      self = this;
      this.init();
      var els;
      var id = YAHOO.util.Dom.generateId();
      var p = document.createElement('p');
      p.id = id;
      p.innerHTML = msg;
      //console.info(headerId);
      YAHOO.util.Dom.insertAfter(p, YAHOO.util.Dom.get(headerId));
      YAHOO.util.Dom.setStyle(YAHOO.util.Dom.get(id), 'opacity', 0);
      YAHOO.util.Dom.addClass(p, headerClass);
      var anim = new YAHOO.util.Anim(YAHOO.util.Dom.get(id), {opacity: {from:0,to:1, method:YAHOO.util.Easing.easeNone, duration:5}});
      anim.onComplete.subscribe(function(){ 
            var el = this.getEl();
            removeElement = function(){
                var delAnim = new YAHOO.util.Anim(YAHOO.util.Dom.get(id), {height: {to:0, method:YAHOO.util.Easing.easeNone, duration:2}, opacity: {to:0, method:YAHOO.util.Easing.easeNone, duration:2}});
                delAnim.onComplete.subscribe(function(){
                    el.parentNode.removeChild(el);
                    var msgLeft = YAHOO.util.Dom.getElementsByClassName(headerClass, 'p');
                    if(msgLeft.length == 0)
                      self.uninit();
                  }
                );
                delAnim.animate();
              }
            setTimeout(removeElement, fadeTime);
          }
        );
      anim.animate();
    },

    uninit: function()
    {
      
      console.log('uninit');
      var c = YAHOO.util.Dom.get(containerId);
      var anim = new YAHOO.util.Anim(YAHOO.util.Dom.getFirstChild(c), {opacity: {to:0, method:YAHOO.util.Easing.easeNone, duration:3}});
      anim.onComplete.subscribe(function()
          {
            var el = YAHOO.util.Dom.get(containerId);
            el.parentNode.removeChild(el);
            container = null;
            header = null;
          }
        );
      anim.animate();
    }
  }
}

