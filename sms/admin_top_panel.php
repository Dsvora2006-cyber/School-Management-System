        <!-- Main Workspace Area -->
        <main class="workspace">
            <!-- Top Panel Header -->
            <div class="top-panel">
                <div class="welcome-title">
                    <h2 id="workspace-title"><?php echo isset($page_title) ? $page_title : 'Admin Workspace'; ?></h2>
                    <p id="workspace-subtitle"><?php echo isset($page_subtitle) ? $page_subtitle : 'AWS System Workspace'; ?></p>
                </div>
                <div class="top-actions">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search operations...">
                    </div>
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark/light theme" style="margin-left: 0.5rem;">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                    <div class="admin-avatar" style="cursor: pointer;" title="Notifications">
                        <i class="fa-solid fa-bell" style="font-size: 0.95rem;"></i>
                    </div>
                </div>
            </div>
