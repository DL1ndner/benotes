import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Bold, Italic } from '@ckeditor/ckeditor5-basic-styles';
import { List } from '@ckeditor/ckeditor5-list';
import { Link } from '@ckeditor/ckeditor5-link';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';

class NotificationModule {
    constructor() {
    let target = document.getElementById('bodytext');
    const config = {
      licenseKey: 'GPL', toolbar: [ 'bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'link', '|', 'sourceEditing' ],
    }
    ClassicEditor.builtinPlugins = [
      Essentials,
      Bold,
      Italic,
      List,
      Link,
      Paragraph,
      SourceEditing
    ];
    ClassicEditor.create(target, config);
  }
}

export default new NotificationModule();
