import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic';
import { Plugin } from '@ckeditor/ckeditor5-core';
import { Command } from '@ckeditor/ckeditor5-core';
import { ButtonView } from '@ckeditor/ckeditor5-ui';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Bold, Italic } from '@ckeditor/ckeditor5-basic-styles';
import { List } from '@ckeditor/ckeditor5-list';
import { Link } from '@ckeditor/ckeditor5-link';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';


// Get the HTML element with the ID of 'bodytext'.
const element = document.querySelector( '#bodytext' );
// Instantiate the editor using the `create` method.
const editor = await ClassicEditor.create( {
	attachTo: element,
	licenseKey: 'GPL' ,
	toolbar: ['bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'link', '|', 'sourceEditing' ],
	plugins: [
	  Essentials,
      Bold,
      Italic,
      List,
      Link,
      Paragraph,
      SourceEditing
    ]
} );

document.addEventListener('DOMContentLoaded', () => {
    const form = document.forms.namedItem('newNote');

    if (!form) {
        return;
    }

    form.addEventListener('submit', () => {
        rteMove();
    });
});
