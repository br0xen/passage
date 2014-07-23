function validateForm() {
  var dPin = document.getElementById('pin'), pin = dPin.value,
      dDoorId = document.getElementById('door_id'), doorId = dDoorId.value,
      dLength = document.getElementById('length'), length = dLength.value,
      valid = true;
  if(pin.trim() == '') {
    addClass(dPin, 'error');
    valid = false;
  } else {
    removeClass(dPin, 'error');
  }
  if(doorId.trim() == '') {
    addClass(dDoorId, 'error');
    valid = false;
  } else {
    removeClass(dDoorId, 'error');
  }
  if(length.trim() == '' || length == '0') {
    addClass(dLength, 'error');
    valid = false;
  } else {
    removeClass(dLength, 'error');
  }
  return valid;
}

window.onload = function() {
  var entries = document.getElementsByClassName('door_history_entry');
  for(var i in entries) {
    addListener(entries[i], 'click', function() {
      var door_id = this.getAttribute('data-door_id');
      document.getElementById('door_id').value = door_id;
      if(validateForm()) {
        document.getElementById('gen_form').submit();
      } else {
        // Show error message
        console.log("Missing Required Information.");
      }
    });
  }
  ajaxJSON('/pinChecksum',{'type':'POST','data':{'pin':document.getElementById('pin').value}})
      .then(function(r) {
        if(typeof r.checksum != 'undefined') {
          document.getElementById('checksum').value = r.checksum;
        } else {
          document.getElementById('checksum').value = '';
        }
      });;
  addListener(document.getElementById('pin'), 'keyup', function(e) {
    ajaxJSON('/pinChecksum',{'type':'POST','data':{'pin':this.value}})
        .then(function(r) {
          if(typeof r.checksum != 'undefined') {
            document.getElementById('checksum').value = r.checksum;
          } else {
            document.getElementById('checksum').value = '';
          }
        });;
  });
  addListener(document.getElementById('gen_form'), 'submit', function(e){
    if(validateForm()) {
      return true;
    }
    e.preventDefault();
    return false;
  });
  var pw_disp = document.getElementById('password_display');
  if(pw_disp != null) {
    addListener(pw_disp, 'click', function(e){
      this.select();
    });
  }
  var form_inputs = document.querySelectorAll('input.form_input');
  for(var i in form_inputs) {
    addListener(form_inputs[i], 'keyup', function(e) {
      if(this.classList.contains('error')) {
        validateForm();
      }
    });
  }
}

function addListener(element, eventName, handler) {
  if(element.addEventListener) {
    element.addEventListener(eventName, handler, false);
  } else if(element.attachEvent) {
    element.attachEvent('on' + eventName, handler);
  } else {
    element['on'+eventName] = handler;
  }
}

function addClass(element, className) {
  element.classList.add(className);
  return element;
}

function removeClass(element, className) {
  element.classList.remove(className);
  return element;
}

function ajaxJSON(url, params) {
  var promise, d = new Date(), n;
  n = d.getTime();
  var params = (params==undefined)?{'type':'GET'}:params;
  params.type = (params.type==undefined)?'GET':params.type.toUpperCase();
  params.data = (params.data==undefined)?{}:params.data;
  params.encode_data = (params.encode_data==undefined)?true:params.encode_data;
  var p = params.encode_data?paramsToString(params.data):params.data;
  promise = new Promise(function(resolve, reject) {
    var client = new XMLHttpRequest();
    client.open(params.type, url, true);
    client.onreadystatechange = function() {
      if(this.readyState === this.DONE) {
        var resp = (this.response!==undefined)?this.response:this.responseText;
        try {
          var json_resp = JSON.parse(resp);
        } catch(e) {
          json_resp = {"error":"A JSON Parsing Error Occurred."};
        }
        resolve(json_resp);
      }
    };
    client.send(p);
  });
  return promise;
}

function paramsToString(params, parent_key) {
  parent_key = (parent_key==undefined)?"":parent_key;
  var p = "";
  for(var i in params) {
    var key = i;
    if(p.length > 0) { p+="&"; }
    if(typeof params[i] === 'object') {
      if(parent_key.length > 0) { key = parent_key+'['+i+']'; }
      p+=paramsToString(params[i], key);
    } else {
      if(parent_key.length > 0) { key = parent_key+'['+i+']'; }
      p+=key+'='+params[i];
    }
  }
  return p;
}
