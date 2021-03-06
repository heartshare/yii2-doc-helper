$(function() {
	$("textarea.codemirror").each(function() {
		var editor = CodeMirror.fromTextArea($(this).get(0), {
			lineNumbers: true,
			styleActiveLine: true,
			matchBrackets: true,
			mode: "text/x-php",
			theme: 'base16-dark',
			readOnly: true,
			foldGutter: true,
			//viewportMargin: 'Infinity',
    		gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
		});
		// if ($(this).val().split(/\r*\n/).length > 1) {
		// 	editor.foldCode(CodeMirror.Pos(1, 0), {'scanUp': true});
		// }
	});

	/* from: http://jsbin.com/ahaxe */
	$.fn.autoGrowInput = function(o) {

	    o = $.extend({
	        maxWidth: 1000,
	        minWidth: 0,
	        comfortZone: 70
	    }, o);

	    this.filter('input:text').each(function(){

	        var minWidth = o.minWidth || $(this).width(),
	            val = '',
	            input = $(this),
	            testSubject = $('<tester/>').css({
	                position: 'absolute',
	                top: -9999,
	                left: -9999,
	                width: 'auto',
	                fontSize: input.css('fontSize'),
	                fontFamily: input.css('fontFamily'),
	                fontWeight: input.css('fontWeight'),
	                letterSpacing: input.css('letterSpacing'),
	                whiteSpace: 'nowrap'
	            }),
	            check = function() {

	                if (val === (val = input.val())) {return;}

	                // Enter new content into testSubject
	                var escaped = val.replace(/&/g, '&amp;').replace(/\s/g,' ').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	                testSubject.html(escaped);

	                // Calculate new width + whether to change
	                var testerWidth = testSubject.width(),
	                    newWidth = (testerWidth + o.comfortZone) >= minWidth ? testerWidth + o.comfortZone : minWidth,
	                    currentWidth = input.width(),
	                    isValidWidthChange = (newWidth < currentWidth && newWidth >= minWidth)
	                                         || (newWidth > minWidth && newWidth < o.maxWidth);

	                // Animate width
	                if (isValidWidthChange) {
	                    input.width(newWidth);
	                }

	            };

	        testSubject.insertAfter(input);

	        $(this).bind('keyup keydown blur update', check);

	    });

	    return this;
	};

	$("input[data-autogrow]").each(function() {
		var options = $(this).data('autogrow');
		$(this).autoGrowInput(options);
	});
});