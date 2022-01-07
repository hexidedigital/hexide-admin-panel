import Alpine from "alpinejs";
import TabItems from "./components/tab-items.js";
import passwords from "./components/user/passwords";
import imagePreview from "./components/imagePreview";

Alpine.data('TabItems', TabItems)
Alpine.data('dataPassword', passwords)
Alpine.data('imagePreview', imagePreview)

window.Alpine = Alpine;
Alpine.start()
