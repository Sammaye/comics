import BvFlashMessageComponent from './components/BvFlashMessageComponent';

export default {
    install(Vue, options) {

        const defaults = {
            method: '$flash',
        };

        Object.assign({}, defaults, options);

        let bus = new Vue({
            data() {
                return {
                    // data
                }
            },
            methods: {
                show: function(msg, type) {
                    this.$emit('show', msg, type);
                },
                primary: function(msg) {
                    this.show(msg, 'primary');
                },
                secondary: function(msg) {
                    this.show(msg, 'secondary');
                },
                success: function(msg) {
                    this.show(msg, 'success');
                },
                danger: function(msg) {
                    this.show(msg, 'danger');
                },
                warning: function(msg) {
                    this.show(msg, 'warning');
                },
                info: function(msg) {
                    this.show(msg, 'info');
                },
                light: function(msg) {
                    this.show(msg, 'light');
                },
                dark: function(msg) {
                    this.show(msg, 'dark');
                }
            },
        });

        Vue.prototype[options.method] = bus;

        Vue.component(BvFlashMessageComponent.name, BvFlashMessageComponent);
    }
};


