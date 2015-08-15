/**
 * Turbo thin local storage layer
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
export default class LocalStorage {
    /**
     * @param string namespace Will prefix all keys with this value
     */
    constructor(namespace = '') {
        this.namespace = namespace;
    }

    /**
     * @param string key
     * @param mixed value
     * @param boolean convertToJSON Whether to store as JSON or not
     */
    set(key, value, convertToJSON = true) {
        if (!this.hasLocalStorage) {
            return;
        }

        if (convertToJSON) {
            value = JSON.stringify(value);
        }

        window.localStorage.setItem(this.namespace+key, value);
    }

    /**
     * @param string key
     * @param mixed def The default if localStorage doesn't have the value
     * @param boolean fromJSON Whether to convert from JSON or not
     * @return mixed
     */
    get(key, def = null, fromJSON = true) {
        if (!this.hasLocalStorage) {
            return;
        }

        let val = window.localStorage.getItem(this.namespace+key);

        if (val === null) {
            return def;
        }

        if (fromJSON) {
            return JSON.parse(val);
        }

        return val;
    }

    /**
     * Whether the client has localStorage capabilities or not
     *
     * @return boolean
     */
    hasLocalStorage() {
        if (typeof window == 'undefined' || !('localStorage' in window)) {
            return false;
        }

        try {
            window.localStorage.setItem(this.namespace+'test', 'hello');
            window.localStorage.removeItem(this.namespace+'test');
        } catch (e) {
            return false;
        }

        return true;
    }
}
