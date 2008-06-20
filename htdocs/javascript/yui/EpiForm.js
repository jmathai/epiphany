YAHOO.widget.Logger.enableBrowserConsole();
YAHOO.namespace("formValidator");
YAHOO.formValidator = function()
{
  var debug= false;
  var defs = [];
  var elements = [];
  var form = null;

  var addDef = function(aDef)
  {
    defs.push(aDef);
  };

  var info = function(msg)
  {
    if(debug)
    {
      console.info(msg);
    }
  };
  
  var rules = {
    notBlank: function(el)
    {
      return el.value.length > 0;
    },
    
    maxChars: function(el, max)
    {
      return el.value.length <= max;
    },
    
    // multibyte safe length
    maxLength: function(el, max)
    {
      var charCode, prevCode, prevLen;
      var len = 0;
      var str = el.value;
      for(var i=0; i<str.length; i++)
      {
        charCode = str.charCodeAt(i);
        if(prevCode >= 0xd800 && prevCode <= 0xdbff && charCode >= 0xdc00 && charCode <= 0xdfff)
          charLen = 4 - prevLen;
        else if(charCode <= 0x7f)
          charLen = 1;
        else if(charCode <= 0x7ff)
          charLen = 2;
        else
          charLen = 3;
        
        len += charLen;
        prevCode = charCode;
        prevLen = charLen;
      }
      
      return len <= max;
    }
  };
  
  return {
    init: function(aArgs)
    {
      debug= aArgs.debug;
      defs = aArgs.defs;
      form = YAHOO.util.Dom.get(aArgs.form);
      YAHOO.util.Event.onDOMReady(function(){ 
          this.getElements();
          YAHOO.util.Event.addListener(form, "submit", this.validate, this, true);
        }
        , this, true);
    },
    
    addElement: function(aDef)
    {
      elements[aDef.el] = {"el":aDef.el, "type":aDef.type, "args":aDef.args};
      if(!YAHOO.lang.isUndefined(aDef.msg))
      {
        elements[aDef.el]['msg'] = aDef.msg;
      }

      if(aDef["event"])
      {
        // force to array
        if(!YAHOO.lang.isArray(aDef["event"]))
          aDef["event"] = [aDef["event"]];

        info("field " + aDef['event'].toSource());
        for(var i=0; i<aDef["event"].length; i++)
        {
          info("adding handler " + aDef["event"] + " for " + aDef.el + " validating " + aDef.type);
          YAHOO.util.Event.addListener(form[aDef.el], aDef["event"][i], this.validateField, aDef, this);
        }
      }
    },

    getElements: function()
    {
      for(aDef in defs)
      {
        if(YAHOO.lang.hasOwnProperty(defs, aDef))
        {
          this.addElement(defs[aDef]);
        }
      }
    },

    repopulate: function(aElements)
    {
      for(name in aElements)
      {
        if(YAHOO.lang.hasOwnProperty(aElements, name))
        {
          el = YAHOO.util.Dom.get(name);
          if(el)
          {
            el.value = aElements[name];
          }
        }
      }
    },

    validate: function(e)
    {
      var retval = true;
      for(aEl in defs)
      {
        if(YAHOO.lang.hasOwnProperty(defs, aEl))
        {
          if(this.validateField(e, defs[aEl]))
          {
            info("passed");
          }
          else
          {
            info("failed");
            retval = false;
          }
        }
      }
      
      if(retval == false)
      {
        YAHOO.util.Event.stopEvent(e);
      }
    },
    
    validateField: function(e, aDef)
    {
      if(YAHOO.lang.isUndefined(aDef.args))
        aDef.args = {};
      
      retval = rules[aDef.type](form[aDef.el], aDef.args);

      if(retval)
      {
        this.pass(aDef);
        info("passed");
      }
      else
      {
        this.fail(aDef);
        info("failed");
      }
      
      return retval;
    },

    /* overridable functions */
    fail: function(aDef)
    {
      alert('fail');
    },

    pass: function(aDef)
    {
      alert('pass');
    }
  };
}();
