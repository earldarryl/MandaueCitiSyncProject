import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

NProgress.configure({
    showSpinner: false,
    trickleSpeed: 200,
    minimum: 0.08,
});


document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        open: window.innerWidth >= 1024,
        screen: window.innerWidth,
        toggle() {
            this.open = !this.open;
        },
        updateScreen() {
            this.screen = window.innerWidth;
        }
    });

    Alpine.store('notifications', {
        open: false,
        activeTab: 'unread',

        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
        openDrawer() {
            this.open = true;
        },
        setTab(tab) {
            this.activeTab = tab;
        }
    });

    Alpine.store('modal', {
        open: false,
        toggle() {
            this.open = !this.open;
        },
        show() {
            this.open = true;
        },
        hide() {
            this.open = false;
        }
    });

    window.addEventListener('resize', () => {
        Alpine.store('sidebar').updateScreen();
    });
});

// import * as FilePond from 'filepond';
// import 'filepond/dist/filepond.min.css';
// import FilePondPluginImageEdit from 'filepond-plugin-image-edit';
// import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
// import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
// import 'filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css';
// import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
// import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
// import FilePondPluginImageResize from 'filepond-plugin-image-resize';
// import FilePondPluginImageTransform from 'filepond-plugin-image-transform';
// import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';
// import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
