YAHOO.widget.Logger.enableBrowserConsole();
YAHOO.namespace("formValidator");
YAHOO.formValidator = function()
{
  var debug= false;
  var defs = [];
  var elements = [];

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
      YAHOO.util.Event.onContentReady(aArgs.form, this.getElements, this, true);
      YAHOO.util.Event.addListener(aArgs.form, "submit", this.validate, this, true);
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

        for(var i=0; i<aDef["event"].length; i++)
        {
          info("adding handler " + aDef["event"] + " for " + aDef.el);
          YAHOO.util.Event.addListener(YAHOO.util.Dom.get(aDef.el), aDef["event"][i], this.validateField, this, true);
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

    validate: function(e)
    {
      var retval = true;
      for(aEl in defs)
      {
        if(YAHOO.lang.hasOwnProperty(defs, aEl))
        {
          if(this.validateField(defs[aEl]))
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
    
    validateField: function(arg)
    {
      if(YAHOO.lang.isUndefined(arg.el))
      {
        var el = YAHOO.util.Event.getTarget(arg)
        aDef = elements[el.id];
      }
      else
      {
        aDef = arg;
      }

      if(YAHOO.lang.isUndefined(aDef.args))
        aDef.args = {};
      
      retval = rules[aDef.type](YAHOO.util.Dom.get(aDef.el), aDef.args);

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
