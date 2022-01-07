export default (image = null) => ({
    image: image,
    file: null,
    is_image_deleted: 0,

    get getPreview() {
        return this.image || '/img/800x800.png';
    },

    get fileName() {
        return this.file?.name || 'Choose file';
    },

    setPreview(event) {
        this.is_image_deleted = 0

        this.file = event.target.files[0]

        const reader = new FileReader()
        reader.onload = (e) => {
            this.image = e.target.result
        }

        reader.onprogress = (e) => {
            console.log(e)
        }

        if (this.file) reader.readAsDataURL(this.file)
    },

    removeImage() {
        this.is_image_deleted = 1
        this.file = null
        this.image = null
    }
})
