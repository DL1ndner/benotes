import { CKEditor5 } from '@typo3/ckeditor5-bundle.js';

class NotificationModule {
    constructor() {
        let target = document.getElementById('bodytext');

        const config = {
            toolbar: [ 'bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'sourceEditing', '|', 'link' ],
        }

        CKEditor5.create(target, config);
    }
}

export default new NotificationModule();
