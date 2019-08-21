/**
 * Autocompleter.Local
 *
 * http://digitarald.de/project/autocompleter/
 *
 * @version		1.1.2
 *
 * @license		MIT-style license
 * @author		Thomas Schalch
 * @copyright	Author
 */

Autocompleter.labdb = new Class({

	Extends: Autocompleter.Request.HTML,

	options: {
	    forceSelect: true,
	    minLength: 0,
	    overflow: true,
	    minLength: 0,
	    injectChoice: function(choice) {
		// choice is one <li> element
		var text = choice.getFirst();
		// the first element in this <li> is the <span> with the text
		var value = text.innerHTML;
		// inputValue saves value of the element for later selection
		choice.inputValue = choice.getLast().innerHTML;
		// overrides the html with the marked query value (wrapped in a <span>)
		text.set('html', this.markQueryValue(value));
		// add the mouse events to the <li> element
		this.addChoiceEvents(choice);
	    },
	},


});

