window.commandPalette = window.commandPalette || function (config) {
    return {
        open: false,
        query: '',
        results: [],
        loading: false,
        activeIndex: 0,
        quickNav: config.quickNav,
        searchUrl: config.searchUrl,
        _abort: null,

        openPalette() {
            this.open = true;
            this.activeIndex = 0;
            document.body.classList.add('overflow-y-hidden');
            this.$nextTick(() => this.$refs.searchInput && this.$refs.searchInput.focus());
        },
        close() {
            this.open = false;
            document.body.classList.remove('overflow-y-hidden');
        },
        toggle() {
            this.open ? this.close() : this.openPalette();
        },
        currentList() {
            return this.query.trim() === '' ? this.quickNav : this.results;
        },
        moveActive(delta) {
            const list = this.currentList();
            if (list.length === 0) {
                return;
            }
            this.activeIndex = (this.activeIndex + delta + list.length) % list.length;
        },
        activate() {
            const list = this.currentList();
            const item = list[this.activeIndex];
            if (item && item.url) {
                window.location.href = item.url;
            }
        },
        async runSearch() {
            const q = this.query.trim();
            this.activeIndex = 0;
            if (q === '') {
                this.results = [];
                this.loading = false;
                return;
            }
            if (this._abort) {
                this._abort.abort();
            }
            this._abort = new AbortController();
            this.loading = true;
            try {
                const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    signal: this._abort.signal,
                });
                if (!res.ok) {
                    this.results = [];
                    return;
                }
                const data = await res.json();
                this.results = data.properties || [];
            } catch (e) {
                if (e.name !== 'AbortError') {
                    this.results = [];
                }
            } finally {
                this.loading = false;
            }
        },
    };
};
