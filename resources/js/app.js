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


