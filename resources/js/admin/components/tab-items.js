export default (items = [], suffix = '', push_state = true) => ({
    active_tab: null,
    key_name: null,
    items: items,
    original_title: null,

    init() {
        this.key_name = ['active_tab', suffix].filter(v => v).join('_');
        this.original_title = document.title
        this.active_tab = new URLSearchParams(window.location.search).get(this.key_name) || this.items[0]?.name;
    },

    isActive(item) {
        if (this.active_tab) {
            return item === this.active_tab
        } else {
            return this.getItem(item) === this.items[0] && this.setActive(this.items[0]?.name)
        }
    },

    setActive(item) {
        if (!this.getItem(item)) item = this.items[0]?.name

        if (!suffix || push_state) {
            const searchParams = new URLSearchParams(window.location.search);
            searchParams.set(this.key_name, item)

            const pathname = window.location.pathname + '?' + searchParams.toString()

            window.history.replaceState(null, null, pathname);
        }

        this.active_tab = item

        return true;
    },

    getItem(item) {
        return this.items.filter((el) => el.name === item)[0]
    }
})
