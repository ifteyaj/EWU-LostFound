<?php
/**
 * Pagination Helper
 * Handles pagination logic and link generation
 */

class Pagination {
    private $currentPage;
    private $itemsPerPage;
    private $totalItems;
    private $totalPages;
    private $urlParams;

    /**
     * Constructor
     * 
     * @param int $totalItems Total number of items
     * @param int $itemsPerPage Items to show per page
     * @param int $currentPage Current page number
     * @param array $urlParams Additional URL parameters (e.g. search query)
     */
    public function __construct($totalItems, $itemsPerPage = 12, $currentPage = 1, $urlParams = []) {
        $this->totalItems = (int)$totalItems;
        $this->itemsPerPage = (int)$itemsPerPage;
        $this->currentPage = max(1, (int)$currentPage);
        $this->urlParams = $urlParams;
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        
        // Ensure current page is valid
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
    }

    /**
     * Get the OFFSET for SQL query
     */
    public function getOffset() {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    /**
     * Get the LIMIT for SQL query
     */
    public function getLimit() {
        return $this->itemsPerPage;
    }

    /**
     * Generate pagination links HTML
     */
    public function getLinks() {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<div class="pagination">';
        
        // Previous Button
        if ($this->currentPage > 1) {
            $html .= '<a href="' . $this->getUrl($this->currentPage - 1) . '" class="page-link">&laquo; Prev</a>';
        } else {
            $html .= '<span class="page-link disabled">&laquo; Prev</span>';
        }

        // Page Numbers
        $start = max(1, $this->currentPage - 2);
        $end = min($this->totalPages, $this->currentPage + 2);

        if ($start > 1) {
            $html .= '<a href="' . $this->getUrl(1) . '" class="page-link">1</a>';
            if ($start > 2) {
                $html .= '<span class="page-dots">...</span>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            $activeClass = ($i == $this->currentPage) ? 'active' : '';
            $html .= '<a href="' . $this->getUrl($i) . '" class="page-link ' . $activeClass . '">' . $i . '</a>';
        }

        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) {
                $html .= '<span class="page-dots">...</span>';
            }
            $html .= '<a href="' . $this->getUrl($this->totalPages) . '" class="page-link">' . $this->totalPages . '</a>';
        }

        // Next Button
        if ($this->currentPage < $this->totalPages) {
            $html .= '<a href="' . $this->getUrl($this->currentPage + 1) . '" class="page-link">Next &raquo;</a>';
        } else {
            $html .= '<span class="page-link disabled">Next &raquo;</span>';
        }

        $html .= '</div>';
        
        return $html;
    }

    /**
     * Helper to build URL with query params
     */
    private function getUrl($page) {
        $params = array_merge($this->urlParams, ['page' => $page]);
        return '?' . http_build_query($params);
    }
}
?>
