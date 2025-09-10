import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

NProgress.configure({
    showSpinner: false,
    trickleSpeed: 200,
    minimum: 0.08,
});


// Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {

//     NProgress.start();
//     console.log('⏳ Livewire Commit Start');

//     respond(() => {

//     });

//     succeed(({ snapshot, effect }) => {

//         NProgress.done();
//         console.log('✅ Livewire Commit Success');
//     });

//     fail(() => {

//         NProgress.done();
//         console.log('❌ Livewire Commit Fail');
//     });
// });

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


