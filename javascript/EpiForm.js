var EpiForm = Class.create();
EpiForm.prototype = 
{
  initialize: function(_form, _classFail)
  {
    this._form      = _form;
    this._classFail = _classFail;
    
    this._rules     = 
    {
      blank: this._valBlank,
      checked: this._valChecked,
      emailedu: this._valEmailEdu
    }
    
    this._elements  = [];
    for(var i=0; i<this._form.elements.length; i++)
    {
      _element = this._form.elements[i];
      /* ie hack */
      if(_element.id == '')
      {
        rand = _element.name + parseInt(Math.random() * 100000);
        _element.id = rand;
        _element = $(_element.id);
      }
      else
      {
        _element = $(_element.id);
      }
      /* // ie hack  */
      
      if(_element.readAttribute('epiform') != null)
      {
        if(this._rules[_element.readAttribute('epiform')] != undefined)
        {
          this._elements.push([_element, _element.readAttribute('epiform')]);
          //console.info('Add %s.', _element.readAttribute('name'));
        }
        else
        {
          //console.info('Element %s does not have a valid epiform attribute.', _element.readAttribute('name'));
        }
      }
      else
      {
        //console.info('Element %s does not have epiform attribute.', _element.readAttribute('name'));
      }
    }
    
    this._elementCount  = this._elements.length;
    
    if(this._elementCount > 0)
    {
      //Event.observe(this._form, 'submit', this.validate.bind(this));
      this._form.onsubmit = this.validate.bind(this); // can't use prototype's writeAttribute
    }
    
    //console.info(this._rules.length);
  },
  
  debug: function()
  {
    
  },
  
  validate: function()
  {
    var retval = true;
    var fail = [];
    var pass = []
    for(var i=0; i<this._elements.length; i++)
    {
      element = this._elements[i][0];
      check   = this._elements[i][1];
      if(!this._rules[check](element))
      {
        fail.push(element);
        retval = false;
      }
      else
      {
        pass.push(element);
      }
      //console.info('Check %s for %s.', element, check);
    }
    
    if(retval == false)
    {
      for(var j=0; j<fail.length; j++)
      {
        element = fail[j];
        if(element.readAttribute('type') != 'radio' && element.readAttribute('type') != 'checkbox')
        {
          element.addClassName(this._classFail);
        }
        else
        {
          //alert(element.readAttribute('msg'));
          element.addClassName(this._classFail);
        }
      }
      
      Form.Element.focus(fail[0]);
    }
    
    //console.info('%i fields passed.', pass.length);
    
    for(var k=0; k<pass.length; k++)
    {
      element = pass[k];
      element.removeClassName(this._classFail);
      //console.info('Pass field %s.', element.readAttribute('name'));
    }
    
    return retval;
  },
  
  _valBlank: function(element)
  {
    return element.value.length > 0;
  },
  
  _valChecked: function(element)
  {
    return element.checked;
  },
  
  _valEmailEdu: function(element)
  {
    var re = /^[A-z0-9._\-]+@[A-z0-9.\-]+\.edu$/;
    return re.test(element.value);
  }
}
