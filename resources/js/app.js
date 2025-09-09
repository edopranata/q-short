import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';

// PrimeVue imports
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css';

// PrimeVue components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import ConfirmDialog from 'primevue/confirmdialog';
import ConfirmationService from 'primevue/confirmationservice';
import Dropdown from 'primevue/dropdown';
import Menu from 'primevue/menu';
import Menubar from 'primevue/menubar';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';
import Chip from 'primevue/chip';
import ProgressBar from 'primevue/progressbar';
import Skeleton from 'primevue/skeleton';
import Divider from 'primevue/divider';
import Panel from 'primevue/panel';
import Toolbar from 'primevue/toolbar';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';
import InlineMessage from 'primevue/inlinemessage';
import ToggleButton from 'primevue/togglebutton';
import Textarea from 'primevue/textarea';
import FloatLabel from 'primevue/floatlabel';
import DatePicker from 'primevue/datepicker';
import Tooltip from 'primevue/tooltip';
import Ripple from 'primevue/ripple';

import { useThemeStore } from './stores/theme';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        app.use(plugin);
        app.use(ZiggyVue);
        app.use(pinia);
        app.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    prefix: 'p',
                    darkModeSelector: '.dark',
                    cssLayer: false
                }
            }
        });
        app.use(ToastService);
        app.use(ConfirmationService);
        app.directive('tooltip', Tooltip);
        app.directive('ripple', Ripple);
        
        // Register PrimeVue components globally
        app.component('Button', Button);
        app.component('InputText', InputText);
        app.component('Card', Card);
        app.component('DataTable', DataTable);
        app.component('Column', Column);
        app.component('Dialog', Dialog);
        app.component('Toast', Toast);
        app.component('ConfirmDialog', ConfirmDialog);
        app.component('Dropdown', Dropdown);
        app.component('Menu', Menu);
        app.component('Menubar', Menubar);
        app.component('Avatar', Avatar);
        app.component('Badge', Badge);
        app.component('Chip', Chip);
        app.component('ProgressBar', ProgressBar);
        app.component('Skeleton', Skeleton);
        app.component('Divider', Divider);
        app.component('Panel', Panel);
        app.component('Toolbar', Toolbar);
        app.component('InputGroup', InputGroup);
        app.component('InputGroupAddon', InputGroupAddon);
        app.component('Password', Password);
        app.component('Checkbox', Checkbox);
        app.component('Message', Message);
        app.component('InlineMessage', InlineMessage);
        app.component('ToggleButton', ToggleButton);
        app.component('Textarea', Textarea);
        app.component('FloatLabel', FloatLabel);
        app.component('DatePicker', DatePicker);
        
        // Initialize theme after app is created
        const themeStore = useThemeStore();
        themeStore.initTheme();
        
        return app.mount(el);
    },
    progress: {
        color: '#3b82f6',
    },
});
