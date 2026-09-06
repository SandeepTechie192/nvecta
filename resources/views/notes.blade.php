<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Management System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white text-sm">
                    <i class="fa-solid fa-note-sticky"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">Notes System</h1>
                    <p class="text-xs text-slate-500">PHP Laravel Backend with AI Search & Summarizer</p>
                </div>
            </div>

            <button onclick="openCreateModal()" class="px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-plus"></i>
                <span>Add Note</span>
            </button>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-6 space-y-6">
        
        <!-- Search Dashboard -->
        <section class="bg-white border border-slate-200 rounded-xl p-4 space-y-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                
                <!-- Search Input Bar -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input 
                        type="search" 
                        id="searchInput" 
                        placeholder="Search notes semantically (e.g. 'machine learning', 'database optimization')..." 
                        class="w-full pl-9 pr-24 py-2 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white text-xs"
                        oninput="handleSearchInput(this.value)"
                        onsearch="if(this.value === '') resetSearch()"
                        onkeydown="if(event.key==='Enter') triggerSearch()"
                    >
                    <button 
                        onclick="triggerSearch()" 
                        class="absolute right-1 top-1 bottom-1 px-3 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium flex items-center gap-1 transition-colors"
                    >
                        <span>AI Search</span>
                    </button>
                </div>

                <!-- Reset Search Button -->
                <button 
                    id="clearSearchBtn"
                    onclick="resetSearch()" 
                    class="hidden px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs border border-slate-300 transition-colors flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Show All Notes</span>
                </button>

            </div>

            <!-- Search Status Info -->
            <div id="searchStatus" class="hidden pt-2 flex items-center justify-between text-xs text-slate-500 border-t border-slate-100">
                <span id="searchStatusText"><i class="fa-solid fa-circle-notch fa-spin text-blue-600 mr-1.5"></i>Searching...</span>
                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-200 text-[11px] font-medium">Semantic Vector Match</span>
            </div>
        </section>

        <!-- Section Header -->
        <div class="flex items-center justify-between px-1">
            <h2 id="sectionTitle" class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-list-ul text-blue-600"></i>
                <span>All Notes</span>
            </h2>

            <div class="flex items-center gap-2 text-xs text-slate-600">
                <span>Per page:</span>
                <select id="limitSelect" onchange="changeLimit(this.value)" class="bg-white border border-slate-300 rounded px-2 py-1 text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                </select>
            </div>
        </div>

        <!-- Notes Cards Grid -->
        <div id="notesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Loading Skeletons -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3 animate-pulse">
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-12 bg-slate-100 rounded"></div>
                <div class="h-4 bg-slate-200 rounded w-1/2"></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3 animate-pulse">
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-12 bg-slate-100 rounded"></div>
                <div class="h-4 bg-slate-200 rounded w-1/2"></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3 animate-pulse">
                <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                <div class="h-12 bg-slate-100 rounded"></div>
                <div class="h-4 bg-slate-200 rounded w-1/2"></div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div id="paginationContainer" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-200 text-xs">
            <div class="text-slate-500" id="paginationInfo">
                Showing notes...
            </div>
            <div class="flex items-center gap-2">
                <button 
                    id="prevPageBtn" 
                    onclick="changePage(-1)" 
                    class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 font-medium border border-slate-300 transition-colors flex items-center gap-1"
                >
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span>Previous</span>
                </button>
                <span id="currentPageBadge" class="px-3 py-1 rounded bg-slate-100 text-slate-700 font-medium border border-slate-200">
                    Page 1
                </span>
                <button 
                    id="nextPageBtn" 
                    onclick="changePage(1)" 
                    class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed text-slate-700 font-medium border border-slate-300 transition-colors flex items-center gap-1"
                >
                    <span>Next</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </main>

    <!-- Create / Edit Note Modal -->
    <div id="noteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/40">
        <div class="bg-white border border-slate-200 w-full max-w-lg rounded-xl p-5 shadow-lg space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 id="modalTitle" class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                    <span>Create Note</span>
                </h3>
                <button onclick="closeNoteModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="noteForm" onsubmit="saveNote(event)" class="space-y-3">
                <input type="hidden" id="noteId">

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Title</label>
                    <input 
                        type="text" 
                        id="noteTitleInput" 
                        required
                        placeholder="Note title..." 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white"
                    >
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Content</label>
                    <textarea 
                        id="noteContentInput" 
                        rows="5"
                        required
                        placeholder="Write your note here..." 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Tags (Comma-separated)</label>
                    <input 
                        type="text" 
                        id="noteTagsInput" 
                        placeholder="e.g. laravel, php, ai" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-xs focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white"
                    >
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeNoteModal()" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium border border-slate-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="saveNoteBtn" class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium flex items-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Note</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AI Summary Modal -->
    <div id="summaryModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/40">
        <div class="bg-white border border-slate-200 w-full max-w-lg rounded-xl p-5 shadow-lg space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded bg-blue-100 text-blue-700 flex items-center justify-center">
                        <i class="fa-solid fa-align-left text-xs"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">AI Note Summary</h3>
                </div>
                <button onclick="closeSummaryModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-2">
                <h4 id="summaryNoteTitle" class="text-xs font-semibold text-blue-700"></h4>
                <div id="summaryContent" class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-slate-800 text-xs leading-relaxed">
                    <div class="flex items-center gap-2 text-blue-600 text-xs font-medium py-2 justify-center">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Generating AI summary...</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                <span id="summaryMetaBadge" class="text-slate-500">Status: Generating</span>
                <div class="flex items-center gap-2">
                    <button onclick="regenerateSummary()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium border border-slate-300 transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                        <span>Regenerate</span>
                    </button>
                    <button onclick="closeSummaryModal()" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2 pointer-events-none"></div>

    <!-- Frontend Logic Script -->
    <script>
        let currentPage = 1;
        let currentLimit = 10;
        let totalPages = 1;
        let activeSearchQuery = '';
        let activeSummaryNoteId = null;
        let searchDebounceTimer = null;

        // Live Search Input Handler (Debounced 300ms & Auto-Reset on clear)
        function handleSearchInput(value) {
            clearTimeout(searchDebounceTimer);
            const query = value.trim();

            if (query === '') {
                resetSearch();
                return;
            }

            searchDebounceTimer = setTimeout(() => {
                triggerSearch();
            }, 300);
        }

        // Fetch notes list with pagination
        async function fetchNotes(page = 1) {
            currentPage = page;
            
            try {
                const response = await fetch(`/api/notes?page=${page}&limit=${currentLimit}`);
                const data = await response.json();

                if (data.success) {
                    renderNotesGrid(data.data, false);
                    updatePaginationUI(data.meta);
                } else {
                    showToast('Failed to fetch notes', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Error connecting to backend API', 'error');
            }
        }

        // Render notes cards
        function renderNotesGrid(notes, isSearchResult = false) {
            const grid = document.getElementById('notesGrid');
            if (!notes || notes.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full py-10 text-center bg-white border border-slate-200 rounded-xl p-6 space-y-2">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-sm">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <p class="text-slate-700 font-medium text-xs">No notes found</p>
                        <p class="text-slate-400 text-[11px]">Create a note or try another search.</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = notes.map(note => {
                const tags = Array.isArray(note.tags) ? note.tags : [];
                const similarityBadge = isSearchResult && note.similarity_score !== undefined
                    ? `<span class="bg-emerald-50 text-emerald-700 text-[11px] font-medium px-2 py-0.5 rounded border border-emerald-200 flex items-center gap-1">
                        <i class="fa-solid fa-bullseye text-[10px]"></i> ${(note.similarity_score * 100).toFixed(0)}% Match
                       </span>`
                    : '';

                return `
                    <div class="bg-white border border-slate-200 hover:border-slate-300 rounded-xl p-4 flex flex-col justify-between transition-colors">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-slate-900 text-xs leading-snug line-clamp-2">${escapeHtml(note.title)}</h3>
                                ${similarityBadge}
                            </div>

                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-4 whitespace-pre-line">${escapeHtml(note.content)}</p>
                        </div>

                        <div class="pt-3 mt-3 border-t border-slate-100 space-y-2.5">
                            <!-- Tags -->
                            <div class="flex flex-wrap gap-1">
                                ${tags.map(tag => `<span class="bg-slate-100 text-slate-600 text-[10px] font-medium px-2 py-0.5 rounded border border-slate-200">#${escapeHtml(tag)}</span>`).join('')}
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-1">
                                <button onclick="openSummaryModal(${note.id}, '${escapeJs(note.title)}')" class="px-2 py-1 rounded bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-medium border border-blue-200 transition-colors flex items-center gap-1">
                                    <i class="fa-solid fa-align-left text-[10px]"></i>
                                    <span>AI Summary</span>
                                </button>

                                <div class="flex items-center gap-1">
                                    <button onclick="editNote(${note.id})" title="Edit Note" class="p-1 text-slate-400 hover:text-slate-700 rounded hover:bg-slate-100 transition-colors">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <button onclick="deleteNote(${note.id})" title="Delete Note" class="p-1 text-slate-400 hover:text-rose-600 rounded hover:bg-slate-100 transition-colors">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Semantic Search Trigger
        async function triggerSearch() {
            const query = document.getElementById('searchInput').value.trim();
            if (!query) return resetSearch();

            activeSearchQuery = query;
            document.getElementById('searchStatus').classList.remove('hidden');
            document.getElementById('clearSearchBtn').classList.remove('hidden');
            document.getElementById('searchStatusText').innerHTML = `<i class="fa-solid fa-magnifying-glass text-blue-600 mr-1.5"></i>Semantic match for: "<strong>${escapeHtml(query)}</strong>"`;
            document.getElementById('sectionTitle').innerHTML = `<i class="fa-solid fa-bullseye text-blue-600"></i> Search Results`;

            try {
                const response = await fetch(`/api/notes/search?query=${encodeURIComponent(query)}&limit=20`);
                const data = await response.json();

                if (data.success) {
                    renderNotesGrid(data.data, true);
                    document.getElementById('paginationContainer').classList.add('hidden');
                } else {
                    showToast(data.message || 'Search failed', 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Error performing semantic search', 'error');
            }
        }

        function resetSearch() {
            document.getElementById('searchInput').value = '';
            activeSearchQuery = '';
            document.getElementById('searchStatus').classList.add('hidden');
            document.getElementById('clearSearchBtn').classList.add('hidden');
            document.getElementById('sectionTitle').innerHTML = `<i class="fa-solid fa-list-ul text-blue-600"></i> All Notes`;
            document.getElementById('paginationContainer').classList.remove('hidden');
            fetchNotes(1);
        }

        // Save note (Create / Update)
        async function saveNote(event) {
            event.preventDefault();
            const id = document.getElementById('noteId').value;
            const title = document.getElementById('noteTitleInput').value;
            const content = document.getElementById('noteContentInput').value;
            const rawTags = document.getElementById('noteTagsInput').value;
            const tags = rawTags ? rawTags.split(',').map(t => t.trim()).filter(Boolean) : [];

            const payload = { title, content, tags };
            const url = id ? `/api/notes/${id}` : '/api/notes';
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();

                if (data.success) {
                    showToast(id ? 'Note updated' : 'Note created', 'success');
                    closeNoteModal();
                    fetchNotes(currentPage);
                } else {
                    showToast(data.message || 'Error saving note', 'error');
                }
            } catch (err) {
                showToast('API request error', 'error');
            }
        }

        // Edit note setup
        async function editNote(id) {
            try {
                const response = await fetch(`/api/notes/${id}`);
                const data = await response.json();
                if (data.success) {
                    const note = data.data;
                    document.getElementById('noteId').value = note.id;
                    document.getElementById('noteTitleInput').value = note.title;
                    document.getElementById('noteContentInput').value = note.content;
                    document.getElementById('noteTagsInput').value = Array.isArray(note.tags) ? note.tags.join(', ') : '';
                    document.getElementById('modalTitle').innerText = 'Edit Note';
                    document.getElementById('noteModal').classList.remove('hidden');
                }
            } catch (err) {
                showToast('Failed to load note details', 'error');
            }
        }

        // Delete note
        async function deleteNote(id) {
            if (!confirm('Delete this note?')) return;
            try {
                const response = await fetch(`/api/notes/${id}`, { method: 'DELETE' });
                const data = await response.json();
                if (data.success) {
                    showToast('Note deleted', 'success');
                    fetchNotes(currentPage);
                }
            } catch (err) {
                showToast('Error deleting note', 'error');
            }
        }

        // AI Summary Modal
        async function openSummaryModal(noteId, title, force = false) {
            activeSummaryNoteId = noteId;
            document.getElementById('summaryNoteTitle').innerText = title;
            document.getElementById('summaryContent').innerHTML = `
                <div class="flex items-center gap-2 text-blue-600 text-xs font-medium py-2 justify-center">
                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                    <span>Generating summary...</span>
                </div>
            `;
            document.getElementById('summaryModal').classList.remove('hidden');

            try {
                const response = await fetch(`/api/notes/${noteId}/summary${force ? '?force=true' : ''}`, { method: 'POST' });
                const data = await response.json();

                if (data.success) {
                    document.getElementById('summaryContent').innerHTML = `
                        <p class="text-slate-800 text-xs leading-relaxed">${escapeHtml(data.summary)}</p>
                    `;
                    document.getElementById('summaryMetaBadge').innerText = data.cached ? 'Status: Loaded from cache' : 'Status: Generated via AI';
                } else {
                    document.getElementById('summaryContent').innerHTML = `<p class="text-rose-600 text-xs">${escapeHtml(data.message || 'Failed to generate summary.')}</p>`;
                    document.getElementById('summaryMetaBadge').innerText = 'Status: Error';
                }
            } catch (err) {
                document.getElementById('summaryContent').innerHTML = `<p class="text-rose-600 text-xs">API Request failed.</p>`;
            }
        }

        function regenerateSummary() {
            if (activeSummaryNoteId) {
                const title = document.getElementById('summaryNoteTitle').innerText;
                openSummaryModal(activeSummaryNoteId, title, true);
            }
        }

        function closeSummaryModal() {
            document.getElementById('summaryModal').classList.add('hidden');
        }

        // Modal Helpers
        function openCreateModal() {
            document.getElementById('noteId').value = '';
            document.getElementById('noteForm').reset();
            document.getElementById('modalTitle').innerText = 'Create Note';
            document.getElementById('noteModal').classList.remove('hidden');
        }

        function closeNoteModal() {
            document.getElementById('noteModal').classList.add('hidden');
        }

        // Pagination UI
        function updatePaginationUI(meta) {
            totalPages = meta.last_page;
            document.getElementById('paginationInfo').innerText = `Showing ${meta.data ? meta.data.length : 0} of ${meta.total} total notes`;
            document.getElementById('currentPageBadge').innerText = `Page ${meta.current_page} of ${meta.last_page}`;
            document.getElementById('prevPageBtn').disabled = meta.current_page <= 1;
            document.getElementById('nextPageBtn').disabled = !meta.has_more;
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                fetchNotes(newPage);
            }
        }

        function changeLimit(newLimit) {
            currentLimit = parseInt(newLimit);
            fetchNotes(1);
        }

        // Utility: Toast notifications
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-600' : type === 'error' ? 'bg-rose-600' : 'bg-slate-800';
            const icon = type === 'success' ? 'fa-circle-check' : type === 'error' ? 'fa-triangle-exclamation' : 'fa-info-circle';

            toast.className = `px-3 py-2 rounded-lg ${bgClass} text-white font-medium text-xs flex items-center gap-2 shadow pointer-events-auto`;
            toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${escapeHtml(message)}</span>`;

            container.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
            });
        }

        function escapeJs(str) {
            if (!str) return '';
            return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetchNotes(1);
        });
    </script>
</body>
</html>
