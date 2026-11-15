/**
 * Easy Address Field input: the address lines, a country select, coordinates and a map to drag a pin on.
 *
 * Registered as a plain component with a template, which the Control Panel's Vue compiles at runtime,
 * so this file needs no build step. Leaflet (window.L) is loaded from leaflet.js before this script.
 */
(() => {
    // Where the map opens when there are no coordinates yet
    const DEFAULT_CENTER = [50.8791758, 4.6997922];
    const DEFAULT_ZOOM = 8;
    const PIN_ZOOM = 15;

    // Same shape as the Control Panel's inputName(): first segment, then [segment] for the rest
    const inputName = (path) => path[0] + path.slice(1).map((segment) => `[${segment}]`).join('');

    const AddressControl = {
        name: 'EasyAddressField',
        props: {
            control: {type: Object, required: true},
            value: {type: Object, default: null},
            label: {type: String, default: null},
            editable: {type: Boolean, default: false},
            invalid: {type: Boolean, default: false},
            required: {type: Boolean, default: false},
            // Passed to every Control; declared so they don't end up as DOM attributes
            values: {type: Object, default: null},
            errors: {type: [Object, Array], default: null},
            touchedPaths: {type: Object, default: null},
            formScope: {type: [Array, String], default: null},
            formRefreshable: {type: Boolean, default: false},
        },
        emits: ['update:value'],
        data() {
            return {open: false};
        },
        computed: {
            labels() {
                return this.control.props.labels;
            },
        },
        methods: {
            get(key) {
                return this.value?.[key] ?? '';
            },
            name(key) {
                return this.editable ? inputName([...this.control.path, key]) : null;
            },
            update(changes, kind = 'typing') {
                this.$emit('update:value', {...(this.value ?? {}), ...changes}, kind);
            },
            set(key, value) {
                this.update({[key]: value === '' ? null : String(value)});
            },
            openMap() {
                this.open = true;
                this.$nextTick(() => this.renderMap());
            },
            renderMap() {
                const lat = parseFloat(this.get('latitude'));
                const lng = parseFloat(this.get('longitude'));
                const hasPin = !isNaN(lat) && !isNaN(lng);
                const center = hasPin ? [lat, lng] : DEFAULT_CENTER;

                this.map = window.L.map(this.$refs.canvas).setView(center, hasPin ? PIN_ZOOM : DEFAULT_ZOOM);
                window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Data © <a href="https://osm.org/copyright">OpenStreetMap</a>',
                    maxZoom: 18,
                }).addTo(this.map);
                this.marker = window.L.marker(center, {draggable: true}).addTo(this.map);
                this.marker.on('dragend', () => this.map.panTo(this.marker.getLatLng()));
                this.map.on('click', (event) => this.marker.setLatLng(event.latlng));
            },
            closeMap() {
                this.map?.remove();
                this.map = null;
                this.open = false;
            },
            done() {
                const {lat, lng} = this.marker.getLatLng();
                this.update({latitude: String(lat), longitude: String(lng)}, 'discrete');
                this.closeMap();
            },
        },
        beforeUnmount() {
            this.closeMap();
        },
        template: `
            <div class="easyaddressfield">
                <craft-input v-for="input in control.props.inputs" :key="input.key"
                    type="text" size="medium" class="form-control form-field"
                    :style="{gridColumn: 'span ' + Math.round(input.width / 100 * 12)}"
                    :name="name(input.key)"
                    :value="get(input.key)"
                    :label="input.label" label-sr-only
                    :placeholder="input.label"
                    :readonly="!editable"
                    @input="set(input.key, $event.target.value)"></craft-input>

                <craft-select v-if="control.props.countries" class="form-control form-field easyaddressfield__full"
                    :label="labels.country" label-sr-only
                    :disabled="!editable">
                    <select slot="input" class="form-control" :name="name('country')" :disabled="!editable" @change="update({country: $event.target.value}, 'discrete')">
                        <option v-for="option in control.props.countries" :key="option.value" :value="option.value" :selected="option.value === get('country')">{{ option.label }}</option>
                    </select>
                </craft-select>
                <input v-else-if="editable" type="hidden" :name="name('country')" :value="get('country')">

                <template v-for="key in ['latitude', 'longitude']" :key="key">
                    <craft-input v-if="control.props.showCoordinates"
                        type="text" size="medium" class="form-control form-field easyaddressfield__half"
                        :name="name(key)"
                        :value="get(key)"
                        :label="labels[key]" label-sr-only
                        :placeholder="labels[key]"
                        :readonly="!editable"
                        @input="set(key, $event.target.value)"></craft-input>
                    <input v-else-if="editable" type="hidden" :name="name(key)" :value="get(key)">
                </template>

                <div v-if="editable" class="easyaddressfield__full">
                    <button type="button" class="easyaddressfield__pick" @click="openMap">{{ labels.pick }}</button>
                </div>

                <Teleport to="body">
                    <div v-if="open" class="easyaddressfield-modal" role="dialog" aria-modal="true" :aria-label="labels.pick" @keydown.esc="closeMap">
                        <div class="easyaddressfield-modal__panel">
                            <div ref="canvas" class="easyaddressfield-modal__map"></div>
                            <div class="easyaddressfield-modal__footer">
                                <craft-button type="button" variant="outline" size="medium" @click="closeMap">{{ labels.cancel }}</craft-button>
                                <craft-button type="button" variant="primary" size="medium" @click="done">{{ labels.done }}</craft-button>
                            </div>
                        </div>
                    </div>
                </Teleport>
            </div>
        `,
    };

    window.Cp.booting((cp) => {
        cp.$components.register('easy-address-field:address', AddressControl);
    });
})();
