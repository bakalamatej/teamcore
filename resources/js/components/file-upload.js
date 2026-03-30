export default (config = {}) => ({
    modelType: config.modelType,
    modelId: config.modelId,
    uploadUrl: config.uploadUrl,
    categories: config.categories ?? [],
    selectedCategory: '',
    selectedFile: null,
    uploading: false,
    error: null,
    success: false,

    handleFileChange(event) {
        this.selectedFile = event.target.files[0] ?? null;
        this.error = null;
        this.success = false;
    },

    async upload() {
        if (!this.selectedFile || !this.selectedCategory) return;

        this.uploading = true;
        this.error = null;
        this.success = false;

        const formData = new FormData();
        formData.append('file', this.selectedFile);
        formData.append('file_category_id', this.selectedCategory);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res = await fetch(this.uploadUrl, {
                method: 'POST',
                body: formData,
            });

            const data = await res.json();

            if (data.success) {
                this.success = true;
                this.reset();
                this.$dispatch('file-uploaded', { modelType: this.modelType, modelId: this.modelId });
            } else {
                this.error = data.message ?? 'Upload failed.';
            }
        } catch (e) {
            this.error = 'An error occurred during upload.';
        } finally {
            this.uploading = false;
        }
    },

    discard() {
        this.reset();
        this.$dispatch('close');
    },

    reset() {
        this.selectedFile = null;
        this.selectedCategory = '';
        this.error = null;
        if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
        }
    },

    formatSize(bytes) {
        if (!bytes) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    },
});