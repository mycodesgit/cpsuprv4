<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. Dynamic Calendar Implementation ---
        let currentDate = new Date(2026, 8, 10); // Initialized to September 2026 based on dashboard data
        const today = new Date(2026, 8, 10); // Reference date for current day highlight

        // Sample events array (Format: YYYY-MM-DD)
        const sampleEvents = ['2026-09-16', '2026-09-20', '2026-10-01'];

        function renderCalendar(date) {
            const monthYearText = document.getElementById('calendarMonthYear');
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            const year = date.getFullYear();
            const month = date.getMonth();

            // Format month header (e.g., September 2026)
            const monthNames = ["January", "February", "March", "April", "May", "June",
                                "July", "August", "September", "October", "November", "December"];
            monthYearText.innerText = `${monthNames[month]} ${year}`;

            // Get first day index of current month and total days
            const firstDayIndex = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();
            const prevLastDay = new Date(year, month, 0).getDate();

            // 1. Previous Month's Trailing Days
            for (let x = firstDayIndex; x > 0; x--) {
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('text-muted', 'opacity-25');
                dayDiv.innerText = prevLastDay - x + 1;
                calendarDays.appendChild(dayDiv);
            }

            // 2. Current Month Days
            for (let i = 1; i <= totalDays; i++) {
                const dayDiv = document.createElement('div');
                dayDiv.innerText = i;

                // Check if day is today
                if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayDiv.classList.add('day-active');
                }

                // Check for sample events
                const formattedMonth = String(month + 1).padStart(2, '0');
                const formattedDay = String(i).padStart(2, '0');
                const dateStr = `${year}-${formattedMonth}-${formattedDay}`;

                if (sampleEvents.includes(dateStr) && !dayDiv.classList.contains('day-active')) {
                    dayDiv.classList.add('day-event');
                }

                calendarDays.appendChild(dayDiv);
            }

            // 3. Next Month's Leading Days (Pad remaining grid slots to maintain full rows)
            const totalGridSlots = calendarDays.children.length;
            const remainingSlots = (totalGridSlots % 7 === 0) ? 0 : 7 - (totalGridSlots % 7);
            for (let j = 1; j <= remainingSlots; j++) {
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('text-muted', 'opacity-25');
                dayDiv.innerText = j;
                calendarDays.appendChild(dayDiv);
            }
        }

        // Initial render
        renderCalendar(currentDate);

        // Previous Month Button Action
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });

        // Next Month Button Action
        document.getElementById('nextMonth').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });

        // --- 3. Announcement Modal Population ---
        const modalElement = document.getElementById('announcementModal');
        modalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-title');
            const date = button.getAttribute('data-date');
            const content = button.getAttribute('data-content');

            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalDate').innerHTML = `<i class="ti ti-calendar me-1"></i> Posted on ${date}`;
            document.getElementById('modalContent').innerText = content;
        });
    });
</script>
