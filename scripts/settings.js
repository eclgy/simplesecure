/**
 * settings scripts for simplesecure
 * version 1.0
 */

/**
 * initialize the model & view and bind events
 */
jQuery(document).ready(function($) {
	
	KeyChain.init('simplesecure_data','simplesecure-settings-content');
	
	// add a new row to the editor table
	$('#simplesecure-add-key').on('click',function(event){
		event.preventDefault();
		KeyChain.addKey();
	});
	
	// reverse binding on inputs
	$(document).on('change', '.simplesecure-editor',function(event){
		event.preventDefault();
		var id = $(this).attr('data-index');
		var email = $('#ss_email_'+id).val();
		var key = $('#ss_key_'+id).val();
		
		if (email.indexOf('"') > -1 || key.indexOf('"') > -1) {
			alert("Don't use double-quotes.  Mmmkay?\nDouble-quotes are bad.  Mmmkay?");
			KeyChainView.render(KeyChain.getKeys());
			return;
		}
		
		KeyChain.updateKeyAt(id,email,key);
	});
	
	// reverse binding on inputs
	$(document).on('click', '.simplesecure-delete',function(event){
		event.preventDefault();
		if (confirm('Seriously?')) {
			var id = $(this).attr('data-index');
			KeyChain.deleteKeyAt(id);
		}
	});
	
});


/**
 * 
 */
var KeyChainView = (function ($) {

	var container;

	/**
	 * util method to get the string for a row in the table
	 */
	function getRow(i, email, key) {
		return '<tr>'
			+'<td><div><input class="simplesecure-editor" id="ss_email_'+ i +'" data-index="'+ i +'" type="text" value="' + email +'" /></div>'
			+'<div class="simplesecure-delete-container"><a tabindex="-1" class="simplesecure-delete" data-index="' + i +'" href="#"><i class="icon-minus-sign-alt"></i> Delete Key</a></div></td>'
			+'<td><div><textarea class="simplesecure-editor" id="ss_key_'+ i +'" data-index="'+ i +'" style="width:100%;">'+ key +'</textarea></div></td>'
			+'</tr>';
	}
	
	/* public methods */
	return {
		
		init: function(containerId) {
			container = $('#'+containerId);
		},
	
		render: function(keychain) {
			
			var html = '<table>';
			html += '<thead>';
			html += '<th><i class="icon-envelope"></i> Email</th>';
			html += '<th><i class="icon-key"></i> Public GPG Key</th>';
			html += '</thead>';
			html += '<tbody>';
			
			for (var i in keychain) {
				html += getRow(i, keychain[i].email, keychain[i].key);
			}
			
			html += '</tbody>';
			html += '</html>';
			
			if (container.html() != '') {
			    container.fadeOut(150, function() {
			    	container.html(html).fadeIn(150, function() {
			    		$("html, body").animate({ scrollTop: container.position().top + container.height() }, "slow");
			    	})
			    });
			}
			else {
				container.html(html);
			}

		}

	};
	
})(jQuery);


/**
 * 
 */
var KeyChain = (function ($, view) {
	
	var container;
	var keys = [];

	/* public methods */
	return {
		
		init: function(modelContainerId,viewContainerId)
		{
			container = $('#'+modelContainerId);
			keys = JSON.parse( container.val() );
			
			view.init(viewContainerId);
			
			view.render(keys);
		},
		
		getKeys: function() {
			return keys;
		},
		
		addKey: function () {
			keys.push({email:'',key:''});
			view.render(keys);
		},

		updateKeyAt: function(i,email,key) {
			keys[i].email = email;
			keys[i].key = key;
			
			container.val(JSON.stringify(keys));
		},
		
		deleteKeyAt: function(i) {
			keys.splice(i, 1);
			
			container.val(JSON.stringify(keys));
			
			view.render(keys);
		}
	};
	
})(jQuery, KeyChainView);

