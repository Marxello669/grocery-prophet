import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        timeout: { type: Number, default: 5000 }
    }

    connect() {
        setTimeout(() => {
            this.dismiss();
        }, this.timeoutValue);
    }

    dismiss() {
        this.element.classList.add('transition-opacity', 'duration-500', 'opacity-0');
        setTimeout(() => {
            this.element.remove();
        }, 500);
    }
}
