/*
 * Modification History:
 *   06/19/2008 - JM
 *    - Ported from prototype.js to YUI and added server-side validation.
 *  01/15/2007 - JM
 *    - Added mod10 function
 *  03/08/2002 - JM
 *    - Fixed bug associated with using in_array and replaced certain in_array w/ array_search
 *  02/26/2002 - JM
 *    - Converted from cfml to php
 *  07/26/2001 - JM
 *    - Added streamline feature so only needed validation is sent to browser.
 *  06/30/2001 - JM
 *    - Added variable name for function for more than one use on a page.
 *  03/15/2001 - JM
 *    - Added option to output debugging information.  Useful when the tag is throwing javascript errors.
 *    - Added maxElementsToDisplay to keep alert box from becoming too large (vertically).
 *  03/06/2001 - JM
 *    - Added ability to validate for minimum and maximum checkboxes
 *    - Added ability to validate using regular expressions
 *  03/05/2001 - JM
 *    - Added minimumlength, maximumlength, and exact length
 *    - Modified theseFields to replace single quotes with "\'"
 *  02/23/2001 - JM
 *    - Added ability to validate against selection lists
 *  02/20/2001 - JM
 *    - Added ability to accept field names in addition to field numbers
 *    - Modified javascript variable to be assigned form object instead of name/number of form
 *  02/16/2001 - JM
 *    - Added minimumdate and maximumdate validation types
 *  02/15/2001 - JM
 *    - Added minimumnumber and maximumnumber validation types
*/
//YAHOO.widget.Logger.enableBrowserConsole();
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
      //console.info(msg);
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
      form = YAHOO.util.Dom.get(aArgs.form);
    },

    initClientValidation: function(aArgs)
    {
      defs = aArgs.defs;
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
          el = form[name];
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
