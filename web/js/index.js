/* 搜索按钮 */
new Vue({
    el: '#header',
    methods: {
        search_change: function () {
            this.search = !this.search;
        },
        menu_trig: function () {
            this.menuOpen = !this.menuOpen;
            this.navOpen = !this.navOpen;
        }
    },
    data: {
        search: true,
        menuOpen: false,
        navOpen: false,
    },
});
