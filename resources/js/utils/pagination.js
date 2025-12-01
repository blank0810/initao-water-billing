// Standardized Pagination Utility
class PaginationManager {
    constructor(config) {
        this.data = config.data || [];
        this.pageSize = config.pageSize || 10;
        this.currentPage = 1;
        this.renderCallback = config.renderCallback;
        this.ids = {
            pageSize: config.ids?.pageSize || 'pageSize',
            totalRecords: config.ids?.totalRecords || 'totalRecords',
            currentPage: config.ids?.currentPage || 'currentPage',
            totalPages: config.ids?.totalPages || 'totalPages',
            prevBtn: config.ids?.prevBtn || 'prevBtn',
            nextBtn: config.ids?.nextBtn || 'nextBtn'
        };
    }

    setData(data) {
        this.data = data;
        this.currentPage = 1;
        this.render();
    }

    updatePageSize(size) {
        this.pageSize = parseInt(size);
        this.currentPage = 1;
        this.render();
    }

    nextPage() {
        if (this.currentPage < this.getTotalPages()) {
            this.currentPage++;
            this.render();
        }
    }

    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.render();
        }
    }

    getTotalPages() {
        return Math.ceil(this.data.length / this.pageSize);
    }

    getCurrentPageData() {
        const start = (this.currentPage - 1) * this.pageSize;
        const end = start + this.pageSize;
        return this.data.slice(start, end);
    }

    render() {
        const totalPages = this.getTotalPages();
        document.getElementById(this.ids.totalRecords).textContent = this.data.length;
        document.getElementById(this.ids.currentPage).textContent = this.currentPage;
        document.getElementById(this.ids.totalPages).textContent = totalPages || 1;
        
        const prevBtn = document.getElementById(this.ids.prevBtn);
        const nextBtn = document.getElementById(this.ids.nextBtn);
        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage >= totalPages;

        if (this.renderCallback) {
            this.renderCallback(this.getCurrentPageData());
        }
    }
}

export default PaginationManager;
