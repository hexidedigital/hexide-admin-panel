export default (items = []) => ({
    active_tab: null,
    items: items,
    original_title: null,

    init() {
        this.original_title = document.title
        this.active_tab = new URLSearchParams(window.location.search).get('active_tab') || this.items[0]?.name;
    },

    isActive(item) {
        if (this.active_tab) {
            return item === this.active_tab
        } else {
            return this.getItem(item) === this.items[0] && this.setActive(this.items[0].name)
        }
    },

    setActive(item) {
        if (!this.getItem(item)) item = this.items[0].name

        const pathname = window.location.pathname + "?active_tab=" + item;
        const el = this.getItem(item);

        window.history.pushState(null, null, pathname);
        // const title = el.title ? el.title + ' | ' : '';
        // document.title = title + this.original_title;
        this.active_tab = item

        return true;
    },

    getItem(item) {
        return this.items.filter((el) => el.name === item)[0]
    }
})
