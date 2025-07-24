<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">

  <div style="background-color:white; margin:100px auto; width:80%; max-width:700px;">
    <div style="background-color:#EFEFEF; padding:10px;">

      <span style="float:right; cursor:pointer;" onclick="hideEditModal()">×</span>
      <h2>EDIT PERSONAL PROFILE</h2>
    </div>
    <form method="POST" action='controller/UserInfoController.php' enctype="multipart/form-data" style="padding:15px;">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Your first name</div>
          <div class="label-help">Your first name</div>
        </div>
        <div class="form-control">
          <input type="text" placeholder="Your first name" name="firstName" value="<?php echo htmlspecialchars($user->getFirstName() ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Your last name</div>
          <div class="label-help">Your last name</div>
        </div>
        <div class="form-control">
          <input type="text" placeholder="Your last name" name="lastName" value="<?php echo htmlspecialchars($user->getLastName() ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Email</div>
          <div class="label-help">Your email address</div>
        </div>
        <div class="form-control">
          <input disabled type="email" placeholder="Your email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>">
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Username</div>
          <div class="label-help">Your username</div>
        </div>
        <div class="form-control">
          <input disabled type="text" placeholder="@username" name="username" value="<?php echo htmlspecialchars($user->getUsername() ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Job title</div>
          <div class="label-help">Job title</div>
        </div>
        <div class="form-control">
          <input type="text" placeholder="Your job title" name="jobTitle" value="<?php echo htmlspecialchars($user->getJobTitle() ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Profile image</div>
          <div class="label-help">Profile image</div>
        </div>
        <div class="form-control">
          <input type="file" id="profile_picture" name="profile_picture" accept="image/*" >
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Date of birth</div>
          <div class="label-help">Date of birth</div>
        </div>
        <div class="form-control dob-selects">
          <select id="day" name="day" required>
            <option value="" selected disabled>Day</option>
            <script>
              const storedDay = <?php echo $user->getDob() ? (int)date('d', strtotime($user->getDob())) : 'null' ?>;
              for (let i = 1; i <= 31; i++) {
                const selected = storedDay === i ? ' selected' : '';
                document.write('<option value="' + i + '"' + selected + '>' + i + '</option>');
              }
            </script>
          </select>

          <select id="month" name="month" required>
            <option value="" selected disabled>Month</option>
            <script>
              const storedMonth = <?php echo $user->getDob() ? (int)date('m', strtotime($user->getDob())) : 'null' ?>;
              const months = [
                [1, 'January'], [2, 'February'], [3, 'March'], [4, 'April'],
                [5, 'May'], [6, 'June'], [7, 'July'], [8, 'August'],
                [9, 'September'], [10, 'October'], [11, 'November'], [12, 'December']
              ];
              months.forEach(([num, name]) => {
                const selected = storedMonth === num ? ' selected' : '';
                document.write('<option value="' + num + '"' + selected + '>' + name + '</option>');
              });
            </script>
          </select>

          <select id="year" name="year" required>
            <option value="" selected disabled>Year</option>
            <script>
              const storedYear = <?php echo $user->getDob() ? (int)date('Y', strtotime($user->getDob())) : 'null' ?>;
              const currentYear = new Date().getFullYear();
              for (let i = currentYear; i >= 1900; i--) {
                const selected = storedYear === i ? ' selected' : '';
                document.write('<option value="' + i + '"' + selected + '>' + i + '</option>');
              }
            </script>
          </select>
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Your phone number</div>
          <div class="label-help">Your phone number</div>
        </div>
        <div class="form-control">
          <input type="text" placeholder="Phone number" name="phoneNumber" value="<?php echo htmlspecialchars($user->getPhoneNumber() ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <div class="form-label">
          <div class="label-title">Current address</div>
          <div class="label-help">Current address</div>
        </div>
        <div class="form-control">
          <input type="text" placeholder="Current address" name="address" value="<?php echo htmlspecialchars($user->getAddress() ?? ''); ?>">
        </div>
      </div>
      <div style="border-top: 1px dashed #d1d5db; margin: 20px 0;"></div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="hideEditModal()">Cancel</button>
        <button type="submit" class="btn-update" name="update_profile">Update</button>
      </div>
    </form>

  </div>
</div>