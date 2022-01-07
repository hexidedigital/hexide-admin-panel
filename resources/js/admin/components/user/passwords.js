export default () => ({
    generated: false,
    password: null,
    confirm: null,

    get isConfirmed() {
        if (this.password > 0 && this.confirm > 0) {
            return this.password === this.confirm
        } else {
            return true
        }
    },

    generate() {
        let length = 10;
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }

        this.generated = true;
        this.password = result;
        this.confirm = result;
    },

    reset() {
        this.generated = false;
        this.password = null;
        this.confirm = null;
    }
})
