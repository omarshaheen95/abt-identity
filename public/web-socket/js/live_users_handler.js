// Get school ID from meta tag
const SCHOOL_ID = document.querySelector('meta[name="school-id"]').content;
const PLATFORM = 'abt-fluency';

let activeUsers = [];
let gradesData = {};
const isDevelopment = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'live',
    cluster: 'mt1',
    wsHost: isDevelopment ? 'localhost' : 'abt-assessments.com',
    wsPort: isDevelopment ? 6002 : 6002,
    wssPort: isDevelopment ? 6002 : 6002,
    forceTLS: true,
    encrypted: true,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: { 'Accept': 'application/json' }
    },
    authEndpoint: 'https://abt-assessments.com/api/broadcasting/auth',
    activityTimeout: 120000,
    pongTimeout: 30000,
    unavailableTimeout: 10000
});

console.log('🔧 WebSocket Configuration:', {
    isDevelopment,
    wsHost: isDevelopment ? 'localhost' : 'abt-assessments.com',
    wsPort: isDevelopment ? 6002 : 6002,
    wssPort: isDevelopment ? 6002 : 6002,
    forceTLS: true,
    encrypted: true,
    schoolId: SCHOOL_ID
});

// Monitor connection state
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('✅ WebSocket Connected Successfully');
    addToLog('🌐 Connection', 'WebSocket connected successfully', 'system');
});

window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('❌ WebSocket Disconnected');
    addToLog('🌐 Connection', 'WebSocket disconnected', 'system');
});

window.Echo.connector.pusher.connection.bind('error', function(error) {
    console.error('❌ WebSocket Error:', error);
    addToLog('⚠️ Error', `WebSocket error: ${error.type || 'Unknown'}`, 'system');
});

window.Echo.connector.pusher.connection.bind('state_change', function(states) {
    console.log('🔄 WebSocket State Change:', states.previous + ' -> ' + states.current);
});

// Try manual connection if not connected after 5 seconds
setTimeout(() => {
    if (window.Echo.connector.pusher.connection.state !== 'connected') {
        console.log('⚠️ WebSocket not connected after 5 seconds, trying to reconnect...');
        window.Echo.connector.pusher.connect();
    }
}, 5000);

Echo.join('active-users')
    .here((users) => {
        // Filter users for this school only
        activeUsers = users
            .filter(user =>user.platform===PLATFORM && user.type === 'student' && user.user.school.id === SCHOOL_ID)
            .map(user => ({
                ...user,
                // Use the joined_at sent from the student, or fallback to current time
                joined_at: user.joined_at || Date.now()
            }));

        console.log(activeUsers)
        updateDashboard();
        addToLog('📊 Dashboard loaded', `${activeUsers.length} active students found`, 'system');
    })
    .joining((user) => {
        // Only track users from this school
        if (user.platform===PLATFORM && user.type === 'student' && user.user.school.id === SCHOOL_ID) {
            const userWithTime = {
                ...user,
                // Use the joined_at sent from the student, or fallback to current time
                joined_at: user.joined_at || Date.now()
            };
            activeUsers.push(userWithTime);
            updateDashboardOnUserChange('join', userWithTime);
            addToLog('✅ Student Joined', `${user.user.name} (Grade: ${user.user.data.grade}, Section: ${user.user.data.section})`, user.platform, 'joined');
        }
    })
    .leaving((user) => {
        // Only track users from this school
        if (user.platform===PLATFORM && user.type === 'student' && user.user.school.id === SCHOOL_ID) {
            const existingUser = activeUsers.find(u => u.user.id === user.user.id);
            const duration = existingUser ? formatDuration(Date.now() - existingUser.joined_at) : '';

            activeUsers = activeUsers.filter(u => u.user.id !== user.user.id);
            updateDashboardOnUserChange('leave', user);
            addToLog('❌ Student Left', `${user.user.name} (Grade: ${user.user.data.grade}, Section: ${user.user.data.section})`, user.platform, 'left', duration);
        }
    });

function updateDashboard() {
    calculateStats();
    renderGrades();
}

function updateDashboardOnUserChange(action, user) {
    const grade = user.user.data.grade;
    const section = user.user.data.section;

    if (action === 'join') {
        // Add user to gradesData
        if (!gradesData[grade]) {
            gradesData[grade] = {users: [], sections: {}};
            gradesData[grade].users.push(user);
            renderNewGradeCard(grade);
        } else {
            gradesData[grade].users.push(user);
            updateGradeHeader(grade);
        }

        if (!gradesData[grade].sections[section]) {
            gradesData[grade].sections[section] = {
                name: section,
                users: []
            };
            gradesData[grade].sections[section].users.push(user);
            renderNewSectionCard(grade, section);
        } else {
            gradesData[grade].sections[section].users.push(user);
            updateSectionCard(grade, section);
        }
    } else if (action === 'leave') {
        // Remove user from gradesData
        if (gradesData[grade]) {
            gradesData[grade].users = gradesData[grade].users.filter(u => u.user.id !== user.user.id);

            if (gradesData[grade].sections[section]) {
                gradesData[grade].sections[section].users =
                    gradesData[grade].sections[section].users.filter(u => u.user.id !== user.user.id);

                if (gradesData[grade].sections[section].users.length === 0) {
                    delete gradesData[grade].sections[section];
                    removeSectionCard(grade, section);
                } else {
                    updateSectionCard(grade, section);
                }
            }

            if (gradesData[grade].users.length === 0) {
                delete gradesData[grade];
                removeGradeCard(grade);
            } else {
                updateGradeHeader(grade);
            }
        }
    }

    updateOverallStats();
}

function calculateStats() {
    // Calculate unique grade + section combinations
    const uniqueSections = new Set();
    activeUsers.forEach(user => {
        uniqueSections.add(`${user.user.data.grade}_${user.user.data.section}`);
    });

    $('#total-users').text(activeUsers.length);
    $('#total-grades').text(Object.keys(gradesData).length);
    $('#total-sections').text(uniqueSections.size);

    // Group by grade
    gradesData = {};
    activeUsers.forEach(user => {
        const grade = user.user.data.grade;
        if (!gradesData[grade]) {
            gradesData[grade] = {users: [], sections: {}};
        }
        gradesData[grade].users.push(user);

        const section = user.user.data.section;
        if (!gradesData[grade].sections[section]) {
            gradesData[grade].sections[section] = {
                name: section,
                users: []
            };
        }
        gradesData[grade].sections[section].users.push(user);
    });
}

function updateOverallStats() {
    const uniqueSections = new Set();
    activeUsers.forEach(user => {
        uniqueSections.add(`${user.user.data.grade}_${user.user.data.section}`);
    });

    $('#total-users').text(activeUsers.length);
    $('#total-grades').text(Object.keys(gradesData).length);
    $('#total-sections').text(uniqueSections.size);
}

function renderGrades() {
    let html = '';
    Object.keys(gradesData).sort().forEach(grade => {
        const data = gradesData[grade];
        const gradeName = `Grade ${grade}`;

        html += `
            <div class="platform-card" data-grade="${grade}">
                <div class="platform-header" onclick="showGradeUsers('${grade}')">
                    <h3 class="platform-name">${gradeName}</h3>
                    <div>
                        <span class="platform-count">${data.users.length} Students</span>
                        <span class="schools-count">${Object.keys(data.sections).length} Sections</span>
                    </div>
                </div>
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Search sections in ${gradeName}..."
                           onkeyup="filterSections('${grade}', this.value)">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <div class="schools-container">
                    <div class="schools-scroll" id="sections-${grade}">
                        ${renderSections(grade, data.sections)}
                    </div>
                </div>
            </div>
        `;
    });

    $('#grades-list').html(html || '<div class="no-platforms"><i class="fas fa-hourglass-half" style="font-size: 2rem; opacity: 0.3;"></i><p class="mt-2 mb-0">No active grades</p></div>');
}

function renderNewGradeCard(grade) {
    const data = gradesData[grade];
    const gradeName = `Grade ${grade}`;

    const html = `
        <div class="platform-card" data-grade="${grade}">
            <div class="platform-header" onclick="showGradeUsers('${grade}')">
                <h3 class="platform-name">${gradeName}</h3>
                <div>
                    <span class="platform-count">${data.users.length} Students</span>
                    <span class="schools-count">${Object.keys(data.sections).length} Sections</span>
                </div>
            </div>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search sections in ${gradeName}..."
                       onkeyup="filterSections('${grade}', this.value)">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="schools-container">
                <div class="schools-scroll" id="sections-${grade}">
                </div>
            </div>
        </div>
    `;

    $('.no-platforms').remove();
    $('#grades-list').append(html);
    updateGradeHeader(grade);
}

function removeGradeCard(grade) {
    $(`.platform-card[data-grade="${grade}"]`).fadeOut(300, function() {
        $(this).remove();
        if ($('#grades-list .platform-card').length === 0) {
            $('#grades-list').html('<div class="no-platforms"><i class="fas fa-hourglass-half" style="font-size: 2rem; opacity: 0.3;"></i><p class="mt-2 mb-0">No active grades</p></div>');
        }
    });
}

function updateGradeHeader(grade) {
    const data = gradesData[grade];
    const card = $(`.platform-card[data-grade="${grade}"]`);

    card.find('.platform-count').text(`${data.users.length} Students`);
    card.find('.schools-count').text(`${Object.keys(data.sections).length} Sections`);
}

function renderNewSectionCard(grade, section) {
    const sectionData = gradesData[grade].sections[section];
    const html = `
        <div class="school-card" data-section="${section}" data-section-name="${section.toLowerCase()}">
            <div class="school-name" onclick="showSectionUsers('${grade}', '${section}')" style="cursor: pointer;">Section ${section}</div>
            <div class="school-users" onclick="showSectionUsers('${grade}', '${section}')">${sectionData.users.length}</div>
            <div class="school-label">Students</div>
        </div>
    `;

    $(`#sections-${grade}`).append(html);
}

function updateSectionCard(grade, section) {
    const sectionData = gradesData[grade].sections[section];
    const card = $(`#sections-${grade} .school-card[data-section="${section}"]`);

    card.find('.school-users').text(sectionData.users.length);
}

function removeSectionCard(grade, section) {
    $(`#sections-${grade} .school-card[data-section="${section}"]`).fadeOut(300, function() {
        $(this).remove();
    });
}

function renderSections(grade, sections) {
    return Object.keys(sections).sort().map(section => {
        const sectionData = sections[section];
        return `
            <div class="school-card" data-section="${section}" data-section-name="${section.toLowerCase()}">
                <div class="school-name" onclick="showSectionUsers('${grade}', '${section}')" style="cursor: pointer;">Section ${section}</div>
                <div class="school-users" onclick="showSectionUsers('${grade}', '${section}')">${sectionData.users.length}</div>
                <div class="school-label">Students</div>
            </div>
        `;
    }).join('');
}

function filterSections(grade, search) {
    const term = search.toLowerCase();
    $(`#sections-${grade} .school-card`).each(function () {
        const sectionName = $(this).data('section-name');
        $(this).toggle(sectionName.includes(term));
    });
}

function showGradeUsers(grade) {
    const data = gradesData[grade];
    const gradeName = `Grade ${grade}`;

    $('#gradeModalTitle').text(`${gradeName} - (${data.users.length}) Students`);
    $('#gradeUsers').html(renderUsers(data.users));
    $('#gradeSearch').val('');
    $('#gradeModal').modal('show');
}

function showSectionUsers(grade, section) {
    const users = gradesData[grade].sections[section].users;

    $('#sectionModalTitle').text(`Grade ${grade} - Section ${section} - (${users.length}) Students`);
    $('#sectionUsers').html(renderUsers(users));
    $('#sectionSearch').val('');
    $('#sectionModal').modal('show');
}

function formatDuration(ms) {
    const seconds = Math.floor(ms / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);

    if (hours > 0) {
        return `${hours}h ${minutes % 60}m`;
    } else if (minutes > 0) {
        return `${minutes}m`;
    } else {
        return `${seconds}s`;
    }
}

function renderUsers(users) {
    return users.map(user => {
        const duration = user.joined_at ? formatDuration(Date.now() - user.joined_at) : 'N/A';
        const platformName = user.platform.replace('abt-', 'ABT ').replace('-', ' ').toUpperCase();

        return `
            <div class="user-item" data-user-name="${user.user.name.toLowerCase()}">
                <div class="user-name">${user.user.name}</div>
                <div class="user-info">
                    ID: ${user.user.id} |
                    Grade: ${user.user.data.grade} |
                    Section: ${user.user.data.section} |
                    Platform: ${platformName} |
                    Duration: ${duration} |
                    <span class="online-badge">Online</span>
                </div>
            </div>
        `;
    }).join('') || '<div class="text-center py-4 text-muted">No students found</div>';
}

// Activity Log Functions
function addToLog(title, message, platform, type = 'info', duration = null) {
    const logContainer = $('#activity-log');
    const time = new Date().toLocaleTimeString('en-US', {
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    let logClass = 'log-item';
    let statusClass = '';
    let statusText = '';

    if (type === 'joined') {
        logClass += ' log-joined';
        statusClass = 'status-joined';
        statusText = 'JOINED';
    } else if (type === 'left') {
        logClass += ' log-left';
        statusClass = 'status-left';
        statusText = 'LEFT';
    }

    const platformDisplay = platform && platform !== 'system'
        ? `<div class="log-platform">Platform: ${platform.replace('abt-', 'ABT ').replace('-', ' ').toUpperCase()}</div>`
        : '';

    const durationDisplay = duration
        ? `<div class="log-platform">Duration: ${duration}</div>`
        : '';

    const logHtml = `
        <div class="${logClass}">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <div class="log-time">${time}</div>
                ${statusText ? `<div class="log-status ${statusClass}">${statusText}</div>` : ''}
            </div>
            <div class="log-message">${message}</div>

            ${durationDisplay}
        </div>
    `;

    // Remove placeholder if exists
    if (logContainer.find('.text-center').length > 0) {
        logContainer.empty();
    }

    logContainer.prepend(logHtml);

    // Keep only last 50 log entries
    const logItems = logContainer.find('.log-item');
    if (logItems.length > 50) {
        logItems.slice(50).remove();
    }

    // Scroll to top
    logContainer.scrollTop(0);
}

// Search in modals
$('#gradeSearch').on('input', function () {
    filterModalUsers('#gradeUsers', this.value);
});

$('#sectionSearch').on('input', function () {
    filterModalUsers('#sectionUsers', this.value);
});

function filterModalUsers(container, search) {
    const term = search.toLowerCase();
    $(`${container} .user-item`).each(function () {
        const userName = $(this).data('user-name');
        $(this).toggle(userName.includes(term));
    });
}

// Update last update time
function updateLastUpdateTime() {
    const now = new Date();
    document.getElementById('lastUpdate').textContent = now.toLocaleTimeString();
}

// Auto-refresh timestamp every 30 seconds
setInterval(updateLastUpdateTime, 30000);

// Control buttons functionality
document.getElementById('fullscreenBtn').addEventListener('click', function() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.log('Error attempting to enable fullscreen:', err.message);
        });
    } else {
        document.exitFullscreen();
    }
});

document.getElementById('refreshBtn').addEventListener('click', function() {
    const refreshIcon = this.querySelector('i');
    refreshIcon.style.transform = 'rotate(360deg)';
    refreshIcon.style.transition = 'transform 0.5s ease';

    setTimeout(() => {
        refreshIcon.style.transform = 'rotate(0deg)';
    }, 500);

    setTimeout(() => {
        location.reload();
    }, 300);
});

// Update fullscreen button icon based on fullscreen state
document.addEventListener('fullscreenchange', function() {
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const icon = fullscreenBtn.querySelector('i');

    if (document.fullscreenElement) {
        icon.className = 'fas fa-compress';
        fullscreenBtn.title = 'Exit Fullscreen';
    } else {
        icon.className = 'fas fa-expand';
        fullscreenBtn.title = 'Fullscreen';
    }
});

// Initialize last update time on page load
updateLastUpdateTime();

console.log(`🚀 School Dashboard initialized for School ID: ${SCHOOL_ID}`);
