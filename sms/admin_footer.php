    <!-- Student Admission Form Modal (Popup overlay) -->
    <div class="modal-overlay" id="admission-modal-overlay">
        <div class="modal-container" id="admission-modal-container" style="max-width: 680px; padding: 2.25rem;">
            <button class="modal-close" id="admission-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header" style="margin-bottom: 1.5rem;">
                <h2>Student <span>Admission Form</span></h2>
                <p>Register a new student profile in the school system database.</p>
            </div>
            
            <form id="admission-form">
                <!-- Step Indicators -->
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem;" id="step-indicators">
                    <div id="ind-step-1" style="height: 4px; flex: 1; background: var(--primary); border-radius: 2px; transition: background 0.3s;"></div>
                    <div id="ind-step-2" style="height: 4px; flex: 1; background: var(--card-border); border-radius: 2px; transition: background 0.3s;"></div>
                </div>

                <!-- Step 1: Student Personal Details -->
                <div id="step-1" class="form-step">
                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary); border-bottom: 1px solid var(--card-border); padding-bottom: 0.5rem;">Step 1: Student Details</h4>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                        <!-- Student ID -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-id" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Student ID</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-id-card input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-id" class="form-control" placeholder="APX-1092" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- First Name -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-fname" style="font-size: 0.8rem; margin-bottom: 0.25rem;">First Name</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-user input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-fname" class="form-control" placeholder="John" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Last Name -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-lname" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Last Name</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-user input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-lname" class="form-control" placeholder="Doe" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Date of Birth -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-dob" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Date of Birth</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-calendar input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="date" id="student-dob" class="form-control" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Gender -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-gender" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Gender</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-venus-mars input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <select id="student-gender" class="form-control" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px; appearance: none; -webkit-appearance: none;">
                                    <option value="" disabled selected>Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <!-- Blood Group -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-blood" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Blood Group</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-droplet input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <select id="student-blood" class="form-control" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px; appearance: none; -webkit-appearance: none;">
                                    <option value="" disabled selected>Select</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                </select>
                            </div>
                        </div>
                        <!-- Mobile Number -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-mobile" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Mobile Number</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-phone input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="tel" id="student-mobile" class="form-control" placeholder="123-456-7890" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-email-input" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Email</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-envelope input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="email" id="student-email-input" class="form-control" placeholder="john@example.com" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Nationality -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-nationality" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Nationality</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-flag input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-nationality" class="form-control" placeholder="American" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Religion -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-religion" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Religion</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-church input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-religion" class="form-control" placeholder="Christian" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Category -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-category" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Category</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-tags input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <select id="student-category" class="form-control" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px; appearance: none; -webkit-appearance: none;">
                                    <option value="" disabled selected>Select</option>
                                    <option value="General">General</option>
                                    <option value="OBC">OBC</option>
                                    <option value="SC/ST">SC/ST</option>
                                </select>
                            </div>
                        </div>
                        <!-- Standard -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-grade" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Standard</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-graduation-cap input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <select id="student-grade" class="form-control" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px; appearance: none; -webkit-appearance: none;">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11 (Commerce)">11 (Commerce)</option>
                                    <option value="11 (Science)">11 (Science)</option>
                                    <option value="12 (Commerce)">12 (Commerce)</option>
                                    <option value="12 (Science)">12 (Science)</option>
                                </select>
                            </div>
                        </div>
                        <!-- Pin Code -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-pincode" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Pin Code</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-map-pin input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-pincode" class="form-control" placeholder="123456" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- Address -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-address-input" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Address</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-location-dot input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-address-input" class="form-control" placeholder="123 Main St" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- City -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-city" style="font-size: 0.8rem; margin-bottom: 0.25rem;">City</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-city input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-city" class="form-control" placeholder="Springfield" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                        <!-- State -->
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label class="form-label" for="student-state" style="font-size: 0.8rem; margin-bottom: 0.25rem;">State</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-map input-icon" style="font-size: 0.9rem; left: 0.85rem;"></i>
                                <input type="text" id="student-state" class="form-control" placeholder="Illinois" required style="padding: 0.65rem 0.65rem 0.65rem 2.25rem; font-size: 0.85rem; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" id="admission-modal-cancel" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btn-next-step" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">Next Step <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Step 2: Parent Details -->
                <div id="step-2" class="form-step" style="display: none;">
                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary); border-bottom: 1px solid var(--card-border); padding-bottom: 0.5rem;">Step 2: Parent Details</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <!-- Father Name -->
                        <div class="form-group">
                            <label class="form-label" for="father-name">Father's Name</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-user input-icon"></i>
                                <input type="text" id="father-name" class="form-control" placeholder="Robert Doe" required>
                            </div>
                        </div>
                        <!-- Mother Name -->
                        <div class="form-group">
                            <label class="form-label" for="mother-name">Mother's Name</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-user input-icon"></i>
                                <input type="text" id="mother-name" class="form-control" placeholder="Mary Doe" required>
                            </div>
                        </div>
                        <!-- Parent Mobile Number -->
                        <div class="form-group">
                            <label class="form-label" for="parent-phone">Parent Mobile Number</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-phone input-icon"></i>
                                <input type="tel" id="parent-phone" class="form-control" placeholder="123-456-7890" required>
                            </div>
                        </div>
                        <!-- Parent Email -->
                        <div class="form-group">
                            <label class="form-label" for="parent-email">Parent Email</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-envelope input-icon"></i>
                                <input type="email" id="parent-email" class="form-control" placeholder="parent@example.com" required>
                            </div>
                        </div>
                        <!-- Occupation -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label" for="parent-occupation">Occupation</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-briefcase input-icon"></i>
                                <input type="text" id="parent-occupation" class="form-control" placeholder="Engineer, Teacher, Doctor, etc." required>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" id="btn-prev-step"><i class="fa-solid fa-arrow-left"></i> Back</button>
                        <button type="submit" class="btn btn-primary">Submit Admission</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Teacher Appointment Form Modal (Popup overlay) -->
    <div class="modal-overlay" id="teacher-modal-overlay">
        <div class="modal-container" id="teacher-modal-container" style="max-width: 600px; padding: 2.25rem;">
            <button class="modal-close" id="teacher-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header" style="margin-bottom: 1.5rem;">
                <h2>Teacher <span>Appointment Form</span></h2>
                <p>Appoint a new educator profile in the academy system database.</p>
            </div>
            
            <form id="teacher-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <!-- Teacher ID -->
                    <div class="form-group">
                        <label class="form-label" for="teacher-id-input">Teacher ID</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-id-card input-icon"></i>
                            <input type="text" id="teacher-id-input" class="form-control" placeholder="TCH-4029" required>
                        </div>
                    </div>
                    <!-- Standard Assignment -->
                    <div class="form-group">
                        <label class="form-label" for="teacher-grade">Standard Assignment</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-graduation-cap input-icon"></i>
                            <select id="teacher-grade" class="form-control" required style="appearance: none; -webkit-appearance: none;">
                                <option value="" disabled selected>Select Grade</option>
                                <option value="1">Standard 1</option>
                                <option value="2">Standard 2</option>
                                <option value="3">Standard 3</option>
                                <option value="4">Standard 4</option>
                                <option value="5">Standard 5</option>
                                <option value="6">Standard 6</option>
                                <option value="7">Standard 7</option>
                                <option value="8">Standard 8</option>
                                <option value="9">Standard 9</option>
                                <option value="10">Standard 10</option>
                                <option value="11 (Commerce)">Standard 11 (Commerce)</option>
                                <option value="11 (Science)">Standard 11 (Science)</option>
                                <option value="12 (Commerce)">Standard 12 (Commerce)</option>
                                <option value="12 (Science)">Standard 12 (Science)</option>
                            </select>
                        </div>
                    </div>
                    <!-- First Name -->
                    <div class="form-group">
                        <label class="form-label" for="teacher-fname">First Name</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" id="teacher-fname" class="form-control" placeholder="Sarah" required>
                        </div>
                    </div>
                    <!-- Last Name -->
                    <div class="form-group">
                        <label class="form-label" for="teacher-lname">Last Name</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" id="teacher-lname" class="form-control" placeholder="Jenkins" required>
                        </div>
                    </div>
                    <!-- Specialization / Department -->
                    <div class="form-group">
                        <label class="form-label" for="teacher-specialization">Specialization</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-book-open input-icon"></i>
                            <select id="teacher-specialization" class="form-control" required style="appearance: none; -webkit-appearance: none;">
                                <option value="" disabled selected>Select Subject</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="Physics & Chemistry">Physics & Chemistry</option>
                                <option value="English & Literature">English & Literature</option>
                                <option value="Biology">Biology</option>
                                <option value="History & Civics">History & Civics</option>
                                <option value="Economics">Economics</option>
                            </select>
                        </div>
                    </div>
                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="teacher-email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="teacher-email" class="form-control" placeholder="sarah@apexacademy.com" required>
                        </div>
                    </div>
                    <!-- Mobile Number -->
                    <div class="form-group" style="grid-column: span 2; margin-bottom: 0;">
                        <label class="form-label" for="teacher-mobile">Mobile Number</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="tel" id="teacher-mobile" class="form-control" placeholder="111-222-3333" required>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" id="teacher-modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="teacher-submit-btn">Appoint Teacher</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Class Creation Modal (Popup overlay) -->
    <div class="modal-overlay" id="class-modal-overlay">
        <div class="modal-container" id="class-modal-container" style="max-width: 600px; padding: 2.25rem;">
            <button class="modal-close" id="class-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header" style="margin-bottom: 1.5rem;">
                <h2>Class <span>Registration Form</span></h2>
                <p>Register a new academic class and allocate teachers/rooms.</p>
            </div>
            
            <form id="class-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <!-- Class ID -->
                    <div class="form-group">
                        <label class="form-label" for="class-id-input">Class ID</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-id-card input-icon"></i>
                            <input type="text" id="class-id-input" class="form-control" placeholder="CLS-101" required>
                        </div>
                    </div>
                    <!-- Class Name -->
                    <div class="form-group">
                        <label class="form-label" for="class-name-input">Class Name</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-school input-icon"></i>
                            <input type="text" id="class-name-input" class="form-control" placeholder="Grade 10 - Div A" required>
                        </div>
                    </div>
                    <!-- Subject Title -->
                    <div class="form-group">
                        <label class="form-label" for="class-subject-input">Subject Name</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-book input-icon"></i>
                            <input type="text" id="class-subject-input" class="form-control" placeholder="English & Literature" required>
                        </div>
                    </div>
                    <!-- Target Standard -->
                    <div class="form-group">
                        <label class="form-label" for="class-standard-input">Standard</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-graduation-cap input-icon"></i>
                            <select id="class-standard-input" class="form-control" required style="appearance: none; -webkit-appearance: none;">
                                <option value="" disabled selected>Select Standard</option>
                                <option value="1">Standard 1</option>
                                <option value="2">Standard 2</option>
                                <option value="3">Standard 3</option>
                                <option value="4">Standard 4</option>
                                <option value="5">Standard 5</option>
                                <option value="6">Standard 6</option>
                                <option value="7">Standard 7</option>
                                <option value="8">Standard 8</option>
                                <option value="9">Standard 9</option>
                                <option value="10">Standard 10</option>
                                <option value="11 (Commerce)">Standard 11 (Commerce)</option>
                                <option value="11 (Science)">Standard 11 (Science)</option>
                                <option value="12 (Commerce)">Standard 12 (Commerce)</option>
                                <option value="12 (Science)">Standard 12 (Science)</option>
                            </select>
                        </div>
                    </div>
                    <!-- Assign Class Teacher -->
                    <div class="form-group">
                        <label class="form-label" for="class-teacher-input">Assign Teacher</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user-tie input-icon"></i>
                            <select id="class-teacher-input" class="form-control" style="appearance: none; -webkit-appearance: none;">
                                <option value="">Unassigned</option>
                                <?php
                                $sql_t_all = "SELECT `teacher_id`, `first_name`, `last_name` FROM `teachers` ORDER BY `first_name` ASC, `last_name` ASC";
                                $res_t_all = $conn->query($sql_t_all);
                                if ($res_t_all && $res_t_all->num_rows > 0) {
                                    while ($t_row = $res_t_all->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($t_row['teacher_id']) . '">' . htmlspecialchars($t_row['first_name'] . ' ' . $t_row['last_name']) . ' (' . htmlspecialchars($t_row['teacher_id']) . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- Room Number -->
                    <div class="form-group">
                        <label class="form-label" for="class-room-input">Room Number</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-door-open input-icon"></i>
                            <input type="text" id="class-room-input" class="form-control" placeholder="Room 101" required>
                        </div>
                    </div>
                    <!-- Schedule / Timing -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label" for="class-schedule-input">Schedule</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-clock input-icon"></i>
                            <input type="text" id="class-schedule-input" class="form-control" placeholder="Mon-Fri 08:00 AM - 01:30 PM" required>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" id="class-modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="class-submit-btn">Create Class</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Finance Collect Fee Modal (Popup overlay) -->
    <div class="modal-overlay" id="finance-modal-overlay">
        <div class="modal-container" id="finance-modal-container" style="max-width: 600px; padding: 2.25rem;">
            <button class="modal-close" id="finance-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header" style="margin-bottom: 1.5rem;">
                <h2>Fee <span>Collection Form</span></h2>
                <p>Generate a new invoice or record a student fee payment.</p>
            </div>
            
            <form id="finance-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <!-- Invoice Number -->
                    <div class="form-group">
                        <label class="form-label" for="invoice-number-input">Invoice Number</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-file-invoice input-icon"></i>
                            <input type="text" id="invoice-number-input" class="form-control" placeholder="INV-2026-0001" required>
                        </div>
                    </div>
                    <!-- Select Standard -->
                    <div class="form-group">
                        <label class="form-label" for="invoice-student-input">Select Standard</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-graduation-cap input-icon"></i>
                            <select id="invoice-student-input" class="form-control" required style="appearance: none; -webkit-appearance: none;">
                                <option value="" disabled selected>Select Standard</option>
                                <option value="1">Standard 1</option>
                                <option value="2">Standard 2</option>
                                <option value="3">Standard 3</option>
                                <option value="4">Standard 4</option>
                                <option value="5">Standard 5</option>
                                <option value="6">Standard 6</option>
                                <option value="7">Standard 7</option>
                                <option value="8">Standard 8</option>
                                <option value="9">Standard 9</option>
                                <option value="10">Standard 10</option>
                                <option value="11 (Commerce)">Standard 11 (Commerce)</option>
                                <option value="11 (Science)">Standard 11 (Science)</option>
                                <option value="12 (Commerce)">Standard 12 (Commerce)</option>
                                <option value="12 (Science)">Standard 12 (Science)</option>
                            </select>
                        </div>
                    </div>
                    <!-- Fee Title / Description -->
                    <div class="form-group">
                        <label class="form-label" for="invoice-title-input">Description</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-tag input-icon"></i>
                            <input type="text" id="invoice-title-input" class="form-control" placeholder="e.g. Tuition Fee Term 1" required>
                        </div>
                    </div>
                    <!-- Amount -->
                    <div class="form-group">
                        <label class="form-label" for="invoice-amount-input">Amount ($)</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-dollar-sign input-icon"></i>
                            <input type="number" step="0.01" min="0.01" id="invoice-amount-input" class="form-control" placeholder="850.00" required>
                        </div>
                    </div>
                    <!-- Due Date -->
                    <div class="form-group">
                        <label class="form-label" for="invoice-duedate-input">Due Date</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-calendar-days input-icon"></i>
                            <input type="date" id="invoice-duedate-input" class="form-control" required>
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label" for="invoice-status-input">Status</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-circle-question input-icon"></i>
                            <select id="invoice-status-input" class="form-control" required style="appearance: none; -webkit-appearance: none;">
                                <option value="Unpaid">Unpaid</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                    </div>
                    <!-- Payment Method (shown dynamically via Javascript if Paid is selected) -->
                    <div class="form-group" id="payment-method-group" style="grid-column: span 2; display: none;">
                        <label class="form-label" for="invoice-method-input">Payment Method</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-credit-card input-icon"></i>
                            <select id="invoice-method-input" class="form-control" style="appearance: none; -webkit-appearance: none;">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="UPI">UPI</option>
                                <option value="GPay">GPay</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" id="finance-modal-cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="finance-submit-btn">Collect Fee</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Fees Structure Modal (Popup overlay) -->
    <div class="modal-overlay" id="fees-structure-modal-overlay">
        <div class="modal-container" id="fees-structure-modal-container" style="max-width: 700px; padding: 2.25rem;">
            <button class="modal-close" id="fees-structure-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header" style="margin-bottom: 1.5rem;">
                <h2>Fees <span>Structure Directory</span></h2>
                <p>Standardized term fee guidelines and installment plans based on class standards.</p>
            </div>
            
            <h4 style="margin-bottom: 0.75rem; color: var(--primary); font-size: 0.95rem; border-left: 3px solid var(--primary); padding-left: 0.5rem; font-weight: 600;">Term Fee Breakdown</h4>
            <div class="table-container" style="box-shadow: none; padding: 0; background: transparent; border: none; margin-bottom: 1.5rem;">
                <table class="students-table">
                    <thead>
                        <tr style="background: var(--primary-glow);">
                            <th>Grade / Standard</th>
                            <th>Tuition Fee</th>
                            <th>Exam Fee</th>
                            <th>Library & Lab</th>
                            <th style="font-weight: 700; color: var(--primary);">Total Term Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 600;">Standards 1 - 5</td>
                            <td>$450.00</td>
                            <td>$50.00</td>
                            <td>$50.00</td>
                            <td style="font-weight: 700; color: var(--text-main);">$550.00</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Standards 6 - 10</td>
                            <td>$600.00</td>
                            <td>$100.00</td>
                            <td>$100.00</td>
                            <td style="font-weight: 700; color: var(--text-main);">$800.00</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Standards 11 - 12 (Commerce)</td>
                            <td>$800.00</td>
                            <td>$150.00</td>
                            <td>$100.00</td>
                            <td style="font-weight: 700; color: var(--text-main);">$1,050.00</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Standards 11 - 12 (Science)</td>
                            <td>$950.00</td>
                            <td>$150.00</td>
                            <td>$200.00</td>
                            <td style="font-weight: 700; color: var(--text-main);">$1,300.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4 style="margin-bottom: 0.75rem; color: var(--primary); font-size: 0.95rem; border-left: 3px solid var(--primary); padding-left: 0.5rem; font-weight: 600;">Installment Schedule</h4>
            <div class="table-container" style="box-shadow: none; padding: 0; background: transparent; border: none; margin-bottom: 1.5rem;">
                <table class="students-table">
                    <thead>
                        <tr style="background: var(--primary-glow);">
                            <th>Grade / Standard</th>
                            <th>Installment 1</th>
                            <th>Installment 2</th>
                            <th>Installment 3</th>
                            <th style="font-weight: 700; color: var(--primary);">Total Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 600;">Standards 1 - 5</td>
                            <td>$250.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(45%)</span></td>
                            <td>$150.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(27.5%)</span></td>
                            <td>$150.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(27.5%)</span></td>
                            <td style="font-weight: 700; color: var(--text-main);">$550.00</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Standards 6 - 10</td>
                            <td>$350.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(43.75%)</span></td>
                            <td>$250.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(31.25%)</span></td>
                            <td>$200.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(25%)</span></td>
                            <td style="font-weight: 700; color: var(--text-main);">$800.00</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Standards 11 - 12 (Commerce)</td>
                            <td>$450.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(42.8%)</span></td>
                            <td>$300.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(28.6%)</span></td>
                            <td>$300.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(28.6%)</span></td>
                            <td style="font-weight: 700; color: var(--text-main);">$1,050.00</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600;">Standards 11 - 12 (Science)</td>
                            <td>$500.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(38.5%)</span></td>
                            <td>$400.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(30.7%)</span></td>
                            <td>$400.00 <span style="font-size: 0.75rem; color: var(--text-muted);">(30.7%)</span></td>
                            <td style="font-weight: 700; color: var(--text-main);">$1,300.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; gap: 0.5rem; align-items: center; background: rgba(59,130,246,0.1); padding: 0.75rem; border-radius: 6px;">
                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                <span>Note: Installment percentages and schedules represent general policy guidelines. Installment payments can be tracked directly from the invoices registry.</span>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" class="btn btn-secondary" id="fees-structure-modal-ok">Close Window</button>
            </div>
        </div>
    </div>
        </main>
    </div>

    <!-- Script triggers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Theme selector integration
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const htmlElement = document.documentElement;

            const savedTheme = localStorage.getItem('theme') || 'dark';
            setTheme(savedTheme);

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });

            function setTheme(theme) {
                htmlElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'fa-solid fa-sun';
                    themeToggleBtn.style.color = '#eab308';
                } else {
                    themeIcon.className = 'fa-solid fa-moon';
                    themeToggleBtn.style.color = '#4f46e5';
                }
            }

            // Admission Modal Trigger System
            const enrollStudentBtn = document.getElementById('enroll-student-btn');
            const quickAddStudentBtn = document.getElementById('quick-add-student-btn');
            const admissionModalOverlay = document.getElementById('admission-modal-overlay');
            const admissionModalClose = document.getElementById('admission-modal-close');
            const admissionModalCancel = document.getElementById('admission-modal-cancel');
            const admissionForm = document.getElementById('admission-form');
            const tableBody = document.getElementById('students-table-body');
            
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const indStep1 = document.getElementById('ind-step-1');
            const indStep2 = document.getElementById('ind-step-2');
            const btnNextStep = document.getElementById('btn-next-step');
            const btnPrevStep = document.getElementById('btn-prev-step');

            let isEditMode = false;

            // Teacher Modal Trigger System
            const appointTeacherBtn = document.getElementById('appoint-teacher-btn');
            const teacherModalOverlay = document.getElementById('teacher-modal-overlay');
            const teacherModalClose = document.getElementById('teacher-modal-close');
            const teacherModalCancel = document.getElementById('teacher-modal-cancel');
            const teacherForm = document.getElementById('teacher-form');
            const teachersTableBody = document.getElementById('teachers-table-body');

            let isTeacherEditMode = false;

            const openTeacherModal = (editMode = false, teacherData = null) => {
                if (!teacherForm) return;
                teacherForm.reset();
                isTeacherEditMode = editMode;

                const modalHeaderTitle = document.querySelector('#teacher-modal-container h2');
                const modalSubmitBtn = document.getElementById('teacher-submit-btn');
                const teacherIdInput = document.getElementById('teacher-id-input');

                if (isTeacherEditMode && teacherData) {
                    modalHeaderTitle.innerHTML = 'Edit Teacher <span>Profile</span>';
                    modalSubmitBtn.textContent = 'Update Profile';
                    teacherIdInput.readOnly = true;
                    teacherIdInput.style.opacity = '0.7';

                    // Populate form fields
                    teacherIdInput.value = teacherData.teacher_id;
                    document.getElementById('teacher-fname').value = teacherData.first_name;
                    document.getElementById('teacher-lname').value = teacherData.last_name;
                    document.getElementById('teacher-specialization').value = teacherData.specialization;
                    document.getElementById('teacher-email').value = teacherData.email;
                    document.getElementById('teacher-mobile').value = teacherData.mobile_number;
                    document.getElementById('teacher-grade').value = teacherData.standard;
                } else {
                    modalHeaderTitle.innerHTML = 'Teacher <span>Appointment Form</span>';
                    modalSubmitBtn.textContent = 'Appoint Teacher';
                    teacherIdInput.readOnly = false;
                    teacherIdInput.style.opacity = '1';
                }

                teacherModalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeTeacherModal = () => {
                if (teacherModalOverlay) {
                    teacherModalOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            };

            if (appointTeacherBtn) appointTeacherBtn.addEventListener('click', () => openTeacherModal(false));
            if (teacherModalClose) teacherModalClose.addEventListener('click', closeTeacherModal);
            if (teacherModalCancel) teacherModalCancel.addEventListener('click', closeTeacherModal);
            if (teacherModalOverlay) {
                teacherModalOverlay.addEventListener('click', (e) => {
                    if (e.target === teacherModalOverlay) {
                        closeTeacherModal();
                    }
                });
            }

            // Class Modal Trigger System
            const createClassBtn = document.getElementById('create-class-btn');
            const classModalOverlay = document.getElementById('class-modal-overlay');
            const classModalClose = document.getElementById('class-modal-close');
            const classModalCancel = document.getElementById('class-modal-cancel');
            const classForm = document.getElementById('class-form');
            const classesTableBody = document.getElementById('classes-table-body');

            let isClassEditMode = false;

            const openClassModal = (editMode = false, classData = null) => {
                if (!classForm) return;
                classForm.reset();
                isClassEditMode = editMode;

                const modalHeaderTitle = document.querySelector('#class-modal-container h2');
                const modalSubmitBtn = document.getElementById('class-submit-btn');
                const classIdInput = document.getElementById('class-id-input');

                if (isClassEditMode && classData) {
                    modalHeaderTitle.innerHTML = 'Edit Class <span>Profile</span>';
                    modalSubmitBtn.textContent = 'Update Class';
                    classIdInput.readOnly = true;
                    classIdInput.style.opacity = '0.7';

                    // Populate form fields
                    classIdInput.value = classData.class_id;
                    document.getElementById('class-name-input').value = classData.class_name;
                    document.getElementById('class-subject-input').value = classData.subject;
                    document.getElementById('class-standard-input').value = classData.standard;
                    document.getElementById('class-teacher-input').value = classData.teacher_id || '';
                    document.getElementById('class-room-input').value = classData.room_number;
                    document.getElementById('class-schedule-input').value = classData.schedule;
                } else {
                    modalHeaderTitle.innerHTML = 'Class <span>Registration Form</span>';
                    modalSubmitBtn.textContent = 'Create Class';
                    classIdInput.readOnly = false;
                    classIdInput.style.opacity = '1';
                }

                classModalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeClassModal = () => {
                if (classModalOverlay) {
                    classModalOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            };

            if (createClassBtn) createClassBtn.addEventListener('click', () => openClassModal(false));
            if (classModalClose) classModalClose.addEventListener('click', closeClassModal);
            if (classModalCancel) classModalCancel.addEventListener('click', closeClassModal);
            if (classModalOverlay) {
                classModalOverlay.addEventListener('click', (e) => {
                    if (e.target === classModalOverlay) {
                        closeClassModal();
                    }
                });
            }

            // Finance Modal Trigger System
            const collectFeeBtn = document.getElementById('collect-fee-btn');
            const financeModalOverlay = document.getElementById('finance-modal-overlay');
            const financeModalClose = document.getElementById('finance-modal-close');
            const financeModalCancel = document.getElementById('finance-modal-cancel');
            const financeForm = document.getElementById('finance-form');
            const invoicesTableBody = document.getElementById('invoices-table-body');
            const invoiceStatusInput = document.getElementById('invoice-status-input');
            const paymentMethodGroup = document.getElementById('payment-method-group');

            const openFinanceModal = () => {
                if (!financeForm) return;
                financeForm.reset();
                if (paymentMethodGroup) paymentMethodGroup.style.display = 'none';
                
                // Set default invoice number
                const randNum = Math.floor(1000 + Math.random() * 9000);
                const invInput = document.getElementById('invoice-number-input');
                if (invInput) invInput.value = `INV-2026-${randNum}`;

                financeModalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeFinanceModal = () => {
                if (financeModalOverlay) {
                    financeModalOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            };

            if (collectFeeBtn) collectFeeBtn.addEventListener('click', openFinanceModal);
            if (financeModalClose) financeModalClose.addEventListener('click', closeFinanceModal);
            if (financeModalCancel) financeModalCancel.addEventListener('click', closeFinanceModal);
            if (financeModalOverlay) {
                financeModalOverlay.addEventListener('click', (e) => {
                    if (e.target === financeModalOverlay) {
                        closeFinanceModal();
                    }
                });
            }

            // Show/hide payment method selection dynamically in form
            if (invoiceStatusInput) {
                invoiceStatusInput.addEventListener('change', () => {
                    if (invoiceStatusInput.value === 'Paid') {
                        if (paymentMethodGroup) paymentMethodGroup.style.display = 'block';
                    } else {
                        if (paymentMethodGroup) paymentMethodGroup.style.display = 'none';
                    }
                });
            }

            // Fees Structure Modal Triggers
            const feesStructureBtn = document.getElementById('fees-structure-btn');
            const feesStructureOverlay = document.getElementById('fees-structure-modal-overlay');
            const feesStructureClose = document.getElementById('fees-structure-modal-close');
            const feesStructureOk = document.getElementById('fees-structure-modal-ok');

            const openFeesStructureModal = () => {
                if (feesStructureOverlay) {
                    feesStructureOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            };

            const closeFeesStructureModal = () => {
                if (feesStructureOverlay) {
                    feesStructureOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            };

            if (feesStructureBtn) feesStructureBtn.addEventListener('click', openFeesStructureModal);
            if (feesStructureClose) feesStructureClose.addEventListener('click', closeFeesStructureModal);
            if (feesStructureOk) feesStructureOk.addEventListener('click', closeFeesStructureModal);
            if (feesStructureOverlay) {
                feesStructureOverlay.addEventListener('click', (e) => {
                    if (e.target === feesStructureOverlay) {
                        closeFeesStructureModal();
                    }
                });
            }

            const openAdmissionModal = (editMode = false, studentData = null) => {
                if (!admissionForm) return;
                admissionForm.reset();
                isEditMode = editMode;

                const modalHeaderTitle = document.querySelector('#admission-modal-container h2');
                const modalSubmitBtn = document.querySelector('#step-2 button[type="submit"]');
                const studentIdInput = document.getElementById('student-id');

                if (isEditMode && studentData) {
                    modalHeaderTitle.innerHTML = 'Edit Student <span>Profile</span>';
                    modalSubmitBtn.textContent = 'Update Profile';
                    studentIdInput.readOnly = true;
                    studentIdInput.style.opacity = '0.7';

                    // Populate form fields
                    studentIdInput.value = studentData.student_id;
                    document.getElementById('student-fname').value = studentData.first_name;
                    document.getElementById('student-lname').value = studentData.last_name;
                    document.getElementById('student-grade').value = studentData.grade;
                    document.getElementById('student-dob').value = studentData.dob;
                    document.getElementById('student-gender').value = studentData.gender;
                    document.getElementById('student-blood').value = studentData.blood_group;
                    document.getElementById('student-mobile').value = studentData.mobile_number;
                    document.getElementById('student-email-input').value = studentData.email;
                    document.getElementById('student-address-input').value = studentData.address;
                    document.getElementById('student-city').value = studentData.city;
                    document.getElementById('student-state').value = studentData.state;
                    document.getElementById('student-pincode').value = studentData.pin_code;
                    document.getElementById('student-nationality').value = studentData.nationality;
                    document.getElementById('student-religion').value = studentData.religion;
                    document.getElementById('student-category').value = studentData.category;

                    document.getElementById('father-name').value = studentData.father_name;
                    document.getElementById('mother-name').value = studentData.mother_name;
                    document.getElementById('parent-phone').value = studentData.parent_mobile;
                    document.getElementById('parent-email').value = studentData.parent_email;
                    document.getElementById('parent-occupation').value = studentData.parent_occupation;
                } else {
                    modalHeaderTitle.innerHTML = 'Student <span>Admission Form</span>';
                    modalSubmitBtn.textContent = 'Submit Admission';
                    studentIdInput.readOnly = false;
                    studentIdInput.style.opacity = '1';
                }

                if (step1) step1.style.display = 'block';
                if (step2) step2.style.display = 'none';
                if (indStep1) indStep1.style.background = 'var(--primary)';
                if (indStep2) indStep2.style.background = 'var(--card-border)';
                admissionModalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeAdmissionModal = () => {
                if (admissionModalOverlay) {
                    admissionModalOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            };

            if (enrollStudentBtn) enrollStudentBtn.addEventListener('click', () => openAdmissionModal(false));
            if (quickAddStudentBtn) {
                quickAddStudentBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    openAdmissionModal(false);
                });
            }
            if (admissionModalClose) admissionModalClose.addEventListener('click', closeAdmissionModal);
            if (admissionModalCancel) admissionModalCancel.addEventListener('click', closeAdmissionModal);
            if (admissionModalOverlay) {
                admissionModalOverlay.addEventListener('click', (e) => {
                    if (e.target === admissionModalOverlay) {
                        closeAdmissionModal();
                    }
                });
            }

            // Step Navigation Logic
            if (btnNextStep) {
                btnNextStep.addEventListener('click', () => {
                    const step1Inputs = step1.querySelectorAll('input, select');
                    let allValid = true;
                    step1Inputs.forEach(input => {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            allValid = false;
                        }
                    });

                    if (allValid) {
                        if (step1) step1.style.display = 'none';
                        if (step2) step2.style.display = 'block';
                        if (indStep2) indStep2.style.background = 'var(--primary)';
                    }
                });
            }

            if (btnPrevStep) {
                btnPrevStep.addEventListener('click', () => {
                    if (step1) step1.style.display = 'block';
                    if (step2) step2.style.display = 'none';
                    if (indStep2) indStep2.style.background = 'var(--card-border)';
                });
            }

            // Handle Admission Form Submit
            if (admissionForm) {
                admissionForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const formData = new FormData();
                    formData.append('student_id', document.getElementById('student-id').value.trim());
                    formData.append('first_name', document.getElementById('student-fname').value.trim());
                    formData.append('last_name', document.getElementById('student-lname').value.trim());
                    formData.append('grade', document.getElementById('student-grade').value);
                    formData.append('dob', document.getElementById('student-dob').value);
                    formData.append('gender', document.getElementById('student-gender').value);
                    formData.append('blood_group', document.getElementById('student-blood').value);
                    formData.append('mobile_number', document.getElementById('student-mobile').value.trim());
                    formData.append('email', document.getElementById('student-email-input').value.trim());
                    formData.append('address', document.getElementById('student-address-input').value.trim());
                    formData.append('city', document.getElementById('student-city').value.trim());
                    formData.append('state', document.getElementById('student-state').value.trim());
                    formData.append('pin_code', document.getElementById('student-pincode').value.trim());
                    formData.append('nationality', document.getElementById('student-nationality').value.trim());
                    formData.append('religion', document.getElementById('student-religion').value.trim());
                    formData.append('category', document.getElementById('student-category').value);
                    formData.append('father_name', document.getElementById('father-name').value.trim());
                    formData.append('mother_name', document.getElementById('mother-name').value.trim());
                    formData.append('parent_mobile', document.getElementById('parent-phone').value.trim());
                    formData.append('parent_email', document.getElementById('parent-email').value.trim());
                    formData.append('parent_occupation', document.getElementById('parent-occupation').value.trim());

                    const submitUrl = isEditMode ? 'update_student.php' : 'save_student.php';
                    
                    fetch(submitUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeAdmissionModal();

                            Swal.fire({
                                title: isEditMode ? 'Profile Updated!' : 'Admission Successful!',
                                text: isEditMode 
                                    ? `Student profile has been updated in the database.`
                                    : `Student has been registered successfully.`,
                                icon: 'success',
                                confirmButtonColor: 'var(--primary)',
                                confirmButtonText: 'Continue Workspace'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Submission Failed',
                                text: data.error || 'Database operation failed.',
                                icon: 'error',
                                confirmButtonColor: 'var(--accent)'
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Database connection error:', err);
                        Swal.fire({
                            title: 'Network Error',
                            text: 'Could not connect to the database server.',
                            icon: 'error',
                            confirmButtonColor: 'var(--accent)'
                        });
                    });
                });
            }

            // Action Buttons delegation (Students List Page)
            if (tableBody) {
                tableBody.addEventListener('click', (e) => {
                    const deleteBtn = e.target.closest('.delete-student-btn');
                    if (deleteBtn) {
                        const studentId = deleteBtn.getAttribute('data-id');
                        const row = deleteBtn.closest('tr');
                        
                        Swal.fire({
                            title: 'Are you sure?',
                            text: `You are about to delete student ${studentId}. This cannot be undone!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--accent)',
                            cancelButtonColor: 'var(--card-border)',
                            confirmButtonText: 'Yes, Delete Student'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const deleteData = new FormData();
                                deleteData.append('student_id', studentId);
                                
                                fetch('delete_student.php', {
                                    method: 'POST',
                                    body: deleteData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        row.style.opacity = '0';
                                        setTimeout(() => {
                                            row.remove();
                                        }, 600);
                                        Swal.fire('Deleted!', 'Student removed from the database.', 'success');
                                    } else {
                                        Swal.fire('Error', data.error || 'Failed to delete student.', 'error');
                                    }
                                });
                            }
                        });
                        return;
                    }

                    const editBtn = e.target.closest('.edit-student-btn');
                    if (editBtn) {
                        const studentId = editBtn.getAttribute('data-id');
                        Swal.fire({
                            title: 'Loading Profile...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        fetch(`get_student.php?student_id=${studentId}`)
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                openAdmissionModal(true, data.data);
                            } else {
                                Swal.fire('Error', data.error || 'Failed to fetch student.', 'error');
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire('Network Error', 'Connection failed.', 'error');
                        });
                    }
                });
            }

            // Handle Teacher Form Submit
            if (teacherForm) {
                teacherForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const tFormData = new FormData();
                    tFormData.append('teacher_id', document.getElementById('teacher-id-input').value.trim());
                    tFormData.append('first_name', document.getElementById('teacher-fname').value.trim());
                    tFormData.append('last_name', document.getElementById('teacher-lname').value.trim());
                    tFormData.append('specialization', document.getElementById('teacher-specialization').value);
                    tFormData.append('email', document.getElementById('teacher-email').value.trim());
                    tFormData.append('mobile_number', document.getElementById('teacher-mobile').value.trim());
                    tFormData.append('standard', document.getElementById('teacher-grade').value);

                    const teacherSubmitUrl = isTeacherEditMode ? 'update_teacher.php' : 'save_teacher.php';

                    fetch(teacherSubmitUrl, {
                        method: 'POST',
                        body: tFormData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeTeacherModal();
                            Swal.fire({
                                title: isTeacherEditMode ? 'Teacher Updated!' : 'Teacher Appointed!',
                                text: 'Teacher records saved in the database successfully.',
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Submission Failed', data.error || 'Database operation failed.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Network Error', 'Could not connect to the database server.', 'error');
                    });
                });
            }

            // Teacher Action Buttons Delegation (Teachers Page)
            if (teachersTableBody) {
                teachersTableBody.addEventListener('click', (e) => {
                    const deleteTBtn = e.target.closest('.delete-teacher-btn');
                    if (deleteTBtn) {
                        const teacherId = deleteTBtn.getAttribute('data-id');
                        const row = deleteTBtn.closest('tr');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: `Remove teacher ${teacherId}?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--accent)',
                            confirmButtonText: 'Yes, Remove Teacher'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const deleteTData = new FormData();
                                deleteTData.append('teacher_id', teacherId);

                                fetch('delete_teacher.php', {
                                    method: 'POST',
                                    body: deleteTData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        row.style.opacity = '0';
                                        setTimeout(() => { row.remove(); }, 600);
                                        Swal.fire('Removed!', 'Teacher profile removed.', 'success');
                                    } else {
                                        Swal.fire('Error', data.error || 'Failed.', 'error');
                                    }
                                });
                            }
                        });
                        return;
                    }

                    const editTBtn = e.target.closest('.edit-teacher-btn');
                    if (editTBtn) {
                        const teacherId = editTBtn.getAttribute('data-id');
                        Swal.fire({
                            title: 'Loading Profile...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        fetch(`get_teacher.php?teacher_id=${teacherId}`)
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                openTeacherModal(true, data.data);
                            } else {
                                Swal.fire('Error', 'Failed to fetch details.', 'error');
                            }
                        });
                    }
                });
            }

            // Handle Class Form Submit
            if (classForm) {
                classForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const cFormData = new FormData();
                    cFormData.append('class_id', document.getElementById('class-id-input').value.trim());
                    cFormData.append('class_name', document.getElementById('class-name-input').value.trim());
                    cFormData.append('subject', document.getElementById('class-subject-input').value.trim());
                    cFormData.append('standard', document.getElementById('class-standard-input').value);
                    cFormData.append('teacher_id', document.getElementById('class-teacher-input').value);
                    cFormData.append('room_number', document.getElementById('class-room-input').value.trim());
                    cFormData.append('schedule', document.getElementById('class-schedule-input').value.trim());

                    const classSubmitUrl = isClassEditMode ? 'update_class.php' : 'save_class.php';

                    fetch(classSubmitUrl, {
                        method: 'POST',
                        body: cFormData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeClassModal();
                            Swal.fire({
                                title: isClassEditMode ? 'Class Updated!' : 'Class Created!',
                                text: 'Class records saved.',
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Submission Failed', data.error || 'Failed.', 'error');
                        }
                    });
                });
            }

            // Class Action Buttons Delegation (Classes Page)
            if (classesTableBody) {
                classesTableBody.addEventListener('click', (e) => {
                    const deleteCBtn = e.target.closest('.delete-class-btn');
                    if (deleteCBtn) {
                        const classId = deleteCBtn.getAttribute('data-id');
                        const row = deleteCBtn.closest('tr');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: `Remove class ${classId}?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--accent)',
                            confirmButtonText: 'Yes, Remove Class'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const deleteCData = new FormData();
                                deleteCData.append('class_id', classId);

                                fetch('delete_class.php', {
                                    method: 'POST',
                                    body: deleteCData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        row.style.opacity = '0';
                                        setTimeout(() => { row.remove(); }, 600);
                                        Swal.fire('Removed!', 'Class deleted.', 'success');
                                    } else {
                                        Swal.fire('Error', 'Failed.', 'error');
                                    }
                                });
                            }
                        });
                        return;
                    }

                    const editCBtn = e.target.closest('.edit-class-btn');
                    if (editCBtn) {
                        const classId = editCBtn.getAttribute('data-id');
                        Swal.fire({
                            title: 'Loading Class Profile...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        fetch(`get_class.php?class_id=${classId}`)
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                openClassModal(true, data.data);
                            } else {
                                Swal.fire('Error', 'Failed to fetch details.', 'error');
                            }
                        });
                    }
                });
            }

            // Handle Finance Form Submit
            if (financeForm) {
                financeForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const fFormData = new FormData();
                    fFormData.append('invoice_number', document.getElementById('invoice-number-input').value.trim());
                    fFormData.append('student_id', document.getElementById('invoice-student-input').value);
                    fFormData.append('title', document.getElementById('invoice-title-input').value.trim());
                    fFormData.append('amount', document.getElementById('invoice-amount-input').value);
                    fFormData.append('due_date', document.getElementById('invoice-duedate-input').value);
                    fFormData.append('status', document.getElementById('invoice-status-input').value);
                    if (document.getElementById('invoice-status-input').value === 'Paid') {
                        fFormData.append('payment_method', document.getElementById('invoice-method-input').value);
                    }

                    fetch('save_invoice.php', {
                        method: 'POST',
                        body: fFormData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            closeFinanceModal();
                            Swal.fire('Invoice Generated!', 'Billing invoice details recorded successfully.', 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Submission Failed', data.error || 'Failed.', 'error');
                        }
                    });
                });
            }

            // Invoices Directory Operations (Finance Page)
            if (invoicesTableBody) {
                invoicesTableBody.addEventListener('click', (e) => {
                    const payBtn = e.target.closest('.pay-invoice-btn');
                    if (payBtn) {
                        const invNo = payBtn.getAttribute('data-id');
                        const row = payBtn.closest('tr');

                        Swal.fire({
                            title: 'Record Fee Payment',
                            html: `
                                <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: var(--text-main); margin-top: 1rem;">
                                    <label class="form-label" style="display:block; margin-bottom:0.5rem; font-weight:600;">Select Payment Channel</label>
                                    <select id="swal-pay-method" class="form-control" style="appearance:none; -webkit-appearance:none; width:100%; padding:0.75rem; border-radius:8px;">
                                        <option value="Cash">Cash Payment</option>
                                        <option value="Card">Credit/Debit Card</option>
                                        <option value="UPI">UPI Portal Scan</option>
                                        <option value="GPay">GPay Wallet Transfer</option>
                                    </select>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonColor: '#10b981',
                            confirmButtonText: 'Record Payment',
                            background: 'var(--swal-bg)',
                            color: 'var(--text-main)'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const payMethod = document.getElementById('swal-pay-method').value;
                                const updateData = new FormData();
                                updateData.append('invoice_number', invNo);
                                updateData.append('status', 'Paid');
                                updateData.append('payment_method', payMethod);

                                fetch('update_invoice_status.php', {
                                    method: 'POST',
                                    body: updateData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Success!', 'Invoice marked as Paid.', 'success').then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire('Failed', data.error || 'Failed.', 'error');
                                    }
                                });
                            }
                        });
                        return;
                    }

                    const deleteInvBtn = e.target.closest('.delete-invoice-btn');
                    if (deleteInvBtn) {
                        const invNo = deleteInvBtn.getAttribute('data-id');
                        const row = deleteInvBtn.closest('tr');

                        Swal.fire({
                            title: 'Delete Invoice?',
                            text: `Remove bill details for ${invNo}?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--accent)',
                            confirmButtonText: 'Yes, Delete'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const deleteInvData = new FormData();
                                deleteInvData.append('invoice_number', invNo);

                                fetch('delete_invoice.php', {
                                    method: 'POST',
                                    body: deleteInvData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        row.style.opacity = '0';
                                        setTimeout(() => { row.remove(); }, 600);
                                        Swal.fire('Deleted!', 'Invoice details removed.', 'success').then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire('Error', 'Failed.', 'error');
                                    }
                                });
                            }
                        });
                    }
                });
            }

            // Leaves Approval Operations (Leaves Page)
            const adminLeavesTableBody = document.getElementById('admin-leaves-table-body');
            if (adminLeavesTableBody) {
                adminLeavesTableBody.addEventListener('click', (e) => {
                    const viewBtn = e.target.closest('.view-leave-btn');
                    if (viewBtn) {
                        const id = viewBtn.getAttribute('data-id');
                        const name = viewBtn.getAttribute('data-name');
                        const subject = viewBtn.getAttribute('data-subject');
                        const reason = viewBtn.getAttribute('data-reason');
                        const start = viewBtn.getAttribute('data-start');
                        const end = viewBtn.getAttribute('data-end');
                        const status = viewBtn.getAttribute('data-status');

                        let badgeClass = 'status-active';
                        let badgeIcon = 'fa-circle-check';
                        if (status.toLowerCase() === 'pending') {
                            badgeClass = 'status-pending';
                            badgeIcon = 'fa-spinner';
                        } else if (status.toLowerCase() === 'rejected') {
                            badgeClass = 'status-pending';
                            badgeIcon = 'fa-circle-xmark';
                        }

                        Swal.fire({
                            title: `Leave Application - ${id}`,
                            html: `
                                <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: var(--text-main); margin-top: 1rem;">
                                    <p style="margin-bottom: 0.75rem;"><strong style="color: var(--primary);">Student Name:</strong> ${name}</p>
                                    <p style="margin-bottom: 0.75rem;"><strong style="color: var(--primary);">Subject:</strong> ${subject}</p>
                                    <p style="margin-bottom: 0.75rem;"><strong style="color: var(--primary);">Dates Requested:</strong> ${start} to ${end}</p>
                                    <p style="margin-bottom: 0.75rem;"><strong style="color: var(--primary);">Current Status:</strong> <span class="status-badge ${badgeClass}" style="${status.toLowerCase() === 'rejected' ? 'background: rgba(244,63,94,0.15); color: var(--accent);' : ''}"><i class="fa-solid ${badgeIcon}"></i> ${status}</span></p>
                                    <div style="border-top: 1px solid var(--card-border); padding-top: 1rem; margin-top: 1rem;">
                                        <strong style="color: var(--primary);">Detailed Reason:</strong>
                                        <p style="margin-top: 0.5rem; white-space: pre-wrap; color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">${reason}</p>
                                    </div>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonColor: 'var(--primary)',
                            background: 'var(--swal-bg)',
                            color: 'var(--text-main)',
                            confirmButtonText: 'Got it'
                        });
                        return;
                    }

                    const approveBtn = e.target.closest('.approve-leave-btn');
                    if (approveBtn) {
                        const id = approveBtn.getAttribute('data-id');
                        const row = approveBtn.closest('tr');

                        Swal.fire({
                            title: 'Approve Request?',
                            text: `Are you sure you want to approve leave application ${id}?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#27c93f',
                            confirmButtonText: 'Yes, Approve'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                updateLeaveStatus(id, 'Approved', row);
                            }
                        });
                        return;
                    }

                    const rejectBtn = e.target.closest('.reject-leave-btn');
                    if (rejectBtn) {
                        const id = rejectBtn.getAttribute('data-id');
                        const row = rejectBtn.closest('tr');

                        Swal.fire({
                            title: 'Reject Request?',
                            text: `Are you sure you want to deny leave application ${id}?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--accent)',
                            confirmButtonText: 'Yes, Reject'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                updateLeaveStatus(id, 'Rejected', row);
                            }
                        });
                    }
                });
            }

            function updateLeaveStatus(id, statusVal, rowElement) {
                const updateData = new FormData();
                updateData.append('leave_id', id);
                updateData.append('status', statusVal);

                fetch('update_leave_status.php', {
                    method: 'POST',
                    body: updateData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', `Request status changed to ${statusVal}.`, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Failed', data.error || 'Failed.', 'error');
                    }
                });
            }

            // Handle Settings Form Submit
            const settingsForm = document.getElementById('settings-form');
            if (settingsForm) {
                settingsForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const sFormData = new FormData(settingsForm);

                    Swal.fire({
                        title: 'Saving Settings...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        background: 'var(--swal-bg)',
                        color: 'var(--text-main)'
                    });

                    fetch('save_settings.php', {
                        method: 'POST',
                        body: sFormData
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire('Configuration Saved!', 'Branding and color accent presets updated successfully.', 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Saving Failed', data.error || 'Database write error.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire('Network Error', 'Connection failed.', 'error');
                    });
                });
            }

            // Auto-open modals from URL parameters (e.g. Overview Quick Operations)
            const urlParams = new URLSearchParams(window.location.search);
            const openModalParam = urlParams.get('openModal');
            if (openModalParam === 'admission' && typeof openAdmissionModal === 'function') {
                openAdmissionModal(false);
            } else if (openModalParam === 'teacher' && typeof openTeacherModal === 'function') {
                openTeacherModal(false);
            } else if (openModalParam === 'class' && typeof openClassModal === 'function') {
                openClassModal(false);
            } else if (openModalParam === 'finance' && typeof openFinanceModal === 'function') {
                openFinanceModal();
            }

        });
    </script>
</body>
</html>
