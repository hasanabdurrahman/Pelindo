 @prepend('after-style')
     <style>
         .widget {
             padding-top: 20px;
             padding-right: 10px;
             padding-left: 20px
         }

         .scroll {
             margin: 4px, 4px;
             padding: 4px;
             height: 280px;
             overflow-x: hidden;
             overflow-y: auto;
             text-align: justify;
         }

         .scroll-project {
             margin: 4px, 4px;
             padding: 4px;
             height: 370px;
             overflow-x: hidden;
             overflow-y: auto;
             text-align: justify;
         }

         .wrapper {
             height: 70px;
             overflow: hidden;
             padding-right: 20px;
             padding-bottom: 10px
                 /* Untuk memotong konten yang terlalu panjang */
         }
     </style>
     <div id="render">
         <div class="page-heading">
             <div class="page-title">
                 <div class="row">
                     <div class="col-12 col-md-6 order-md-1 order-last">
                         <h3>Profile</h3>
                         <p class="text-subtitle text-muted"></p>
                     </div>

                 </div>
             </div>
             <section class="section row">
                 <div class="container-xl px-4 mt-4">

                     <hr class="mt-0 mb-4">
                     <div class="row">


                         <div class="col-xl-4">
                             <!-- Profile picture card-->
                             <div class="card mb-4 mb-xl-0">
                                 <div class="card-header">Profile Picture</div>
                                 <div class="card-body text-center">
                                     <!-- Profile picture image-->
                                     @if ($user->picture)
                                         <img class="img-account-profile rounded-circle mb-2" width="200px"
                                             id="preview-image" src="{{ Storage::url('employee/' . $user->picture) }}"
                                             alt="Profile Picture">
                                     @else
                                         <img class="img-account-profile rounded-circle mb-2" width="200px"
                                             id="preview-image" src="{{ asset('assets/images/faces/1.jpg') }}"
                                             alt="Default Profile Picture">
                                     @endif
                                     <!-- Profile picture help block-->
                                     <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                                     <!-- Profile picture upload button-->
                                     <label class="btn btn-primary" for="profile-image">Upload new image</label>
                                     <input type="file" class="form-control-file d-none" id="profile-image"
                                         name="profile_image" onchange="previewImage(this)">
                                 </div>
                             </div>

                         </div>
                         <div class="col-xl-8">
                             <!-- Account details card-->
                             <div class="card mb-4">
                                 <div class="card-header">Account Details</div>
                                 <div class="card-body">
                                     <form id="profile-form" action="{{ route('profile.updateProfile') }}" method="POST">
                                         @csrf
                                         @method('PUT')
                                         <!-- Form Group (username)-->
                                         <div class="mb-3">
                                             <label class="small mb-1" for="inputUsername">NIPP</label>
                                             <input class="form-control" id="inputUsername" type="text"
                                                 placeholder="Enter your username" value="{{ old('name', $user->code) }}"
                                                 @readonly(true)>
                                         </div>
                                         <!-- Form Row-->
                                         <div class="row gx-3 mb-3">
                                             <!-- Form Group (first name)-->
                                             <div class="col-md-6">
                                                 <label class="small mb-1" for="inputFirstName">Nama Lengkap</label>
                                                 <input class="form-control" id="inputFirstName" type="text"
                                                     placeholder="Enter your first name"
                                                     value="{{ old('name', $user->name) }}" @readonly(true)>
                                             </div>
                                             <!-- Form Group (last name)-->
                                             <div class="col-md-6">
                                                 <label class="small mb-1" for="inputLastName">Email</label>
                                                 <input class="form-control" id="inputLastName" name="email" type="email"
                                                     placeholder="Enter your last name"
                                                     value="{{ old('name', $user->email) }}" @readonly(false)>
                                             </div>
                                         </div>



                                         <!-- Form Row-->
                                         <div class="row gx-3 mb-3">
                                             <!-- Form Group (phone number)-->
                                             <div class="col-md-6">
                                                 <label class="small mb-1" for="inputPhone">Roles</label>
                                                 <input class="form-control" id="inputPhone" type="text"
                                                     placeholder="Enter your phone number"
                                                     value="{{ old('name', $user->roles->name) }}" @readonly(true)>
                                             </div>
                                             <!-- Form Group (birthday)-->
                                             <div class="col-md-6">
                                                 <label class="small mb-1" for="inputBirthday">Divisi</label>
                                                 <input class="form-control" id="inputBirthday" type="text"
                                                     name="birthday" placeholder="Enter your birthday"
                                                     value="{{ old('name', $user->division->name) }}" @readonly(true)>
                                             </div>
                                         </div>

                                         <!-- Form Row        -->
                                         <div class="row gx-3 mb-3">
                                             <!-- Form Group (organization name)-->
                                             <div class="col-md-6">
                                                 <label class="small mb-1" for="inputOrgName">Address</label>
                                                 <input class="form-control" name="address" type="text"
                                                     placeholder="Enter your organization name"
                                                     value="{{ old('name', $user->address) }}">
                                             </div>
                                             <!-- Form Group (location)-->
                                             <div class="col-md-6">
                                                 <label class="small mb-1" for="inputLocation">Phone</label>
                                                 <input class="form-control" name="phone" type="tel"
                                                     placeholder="Enter your location"
                                                     value="{{ old('name', $user->phone) }}">
                                             </div>
                                         </div>
                                         <!-- Save changes button-->
                                         <button type="button" id="save-profile" class="btn btn-primary"
                                             type="button">Save changes</button>
                                     </form>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col">
                         <!-- Change password card-->
                         <div class="card mb-4">
                             <div class="card-header">Change Password</div>
                             <div class="card-body">
                                 <form id="password-form" action="{{ route('profile.updatePassword') }}" method="POST">
                                     @csrf
                                     <!-- Form Group (current password)-->
                                     <div class="mb-3">
                                         <label class="small mb-1" for="currentPassword">Current Password</label>
                                         <input class="form-control" id="currentPassword" name="currentPassword"
                                             type="password" placeholder="Enter current password">
                                     </div>
                                     <!-- Form Group (new password)-->
                                     <div class="mb-3">
                                         <label class="small mb-1" for="newPassword">New Password</label>
                                         <input class="form-control" id="newPassword" name="newPassword" type="password"
                                             placeholder="Enter new password">
                                     </div>
                                     <!-- Form Group (confirm password)-->
                                     <div class="mb-3">
                                         <label class="small mb-1" for="confirmPassword">Confirm Password</label>
                                         <input class="form-control" id="confirmPassword" name="confirmPassword"
                                             type="password" placeholder="Confirm new password">
                                     </div>
                                     <button class="btn btn-primary" id="profile-form-password"
                                         type="submit">Save</button>
                                 </form>

                             </div>
                         </div>

                     </div>

                 </div>



             </section>
         </div>
     </div>


     @prepend('after-script')
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <script>
             function previewImage(input) {
                 if (input.files && input.files[0]) {
                     var reader = new FileReader();

                     reader.onload = function(e) {
                         // Mengatur gambar profil di div avatar
                         $('#avatar-image').attr('src', e.target.result);

                         // Mengatur gambar profil di elemen lain (misalnya, gambar lingkaran)
                         $('#preview-image').attr('src', e.target.result);
                     };

                     reader.readAsDataURL(input.files[0]);
                 }
             }
         </script>
         <script>
             $(document).ready(function() {
                 $('#save-profile').click(function() {
                     var form = $('#profile-form');
                     var url = form.attr('action');
                     var formData = new FormData(form[0]); // Membuat objek FormData dari form
                     var profileImageInput = $('#profile-image')[0].files[
                         0]; // Mengambil file gambar dari input file

                     // Menambahkan file gambar ke dalam FormData
                     formData.append('picture', profileImageInput);

                     $.ajax({
                         url: url,
                         type: 'POST', // Ubah jenis permintaan ke POST karena Anda mengunggah file
                         data: formData, // Menggunakan FormData
                         dataType: 'json',
                         processData: false, // Menggunakan false agar FormData tidak diproses
                         contentType: false, // Menggunakan false agar FormData tidak diberi tipe konten
                         success: function(response) {
                             // Menampilkan SweetAlert2 untuk pesan sukses
                             Swal.fire({
                                 icon: 'success',
                                 title: 'Success',
                                 text: response.message,
                             });
                         },
                         error: function(xhr) {
                             if (xhr.status === 422) {
                                 var errors = xhr.responseJSON.errors;
                                 var errorMessages = '';

                                 // Menggabungkan pesan kesalahan dalam satu string
                                 for (var key in xhr.responseJSON.error) {
                                     errorMessages += xhr.responseJSON.error[key][0] + '<br>';
                                 }

                                 // Menampilkan SweetAlert2 untuk pesan kesalahan
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Validation Error',
                                     html: errorMessages,
                                 });
                             } else {
                                 // Menampilkan SweetAlert2 untuk kesalahan umum
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Error',
                                     text: 'Terjadi kesalahan saat memperbarui profil.',
                                 });
                             }
                         }
                     });
                 });
             });
             $(document).ready(function() {
                 $('#password-form').submit(function(event) {
                     event.preventDefault(); // Prevent the form from submitting normally

                     var form = $(this);
                     var formData = new FormData(form[0]);

                     // Mengirim data ke server menggunakan AJAX PUT request.
                     $.ajax({
                         url: form.attr('action'),
                         type: 'POST',
                         data: formData,
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                         processData: false,
                         contentType: false,
                         dataType: 'json',
                         success: function(response) {
                             // Menampilkan SweetAlert2 untuk pesan sukses
                             Swal.fire({
                                 icon: 'success',
                                 title: 'Success',
                                 text: response.message,
                             });

                             $('#currentPassword').val('');
                             $('#newPassword').val('');
                             $('#confirmPassword').val('');
                         },
                         error: function(xhr) {
                             if (xhr.status === 422) {
                                 var errors = xhr.responseJSON
                                 .error; // Akses pesan kesalahan dari objek "error"
                                 var errorMessages = '';

                                 // Loop melalui pesan kesalahan dalam objek "error"
                                 for (var key in errors) {
                                     errorMessages += errors[key][0] + '<br>';
                                 }

                                 // Menampilkan SweetAlert2 untuk pesan kesalahan
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Validation Error',
                                     html: errorMessages,
                                 });
                             } else {
                                 // Menampilkan SweetAlert2 untuk kesalahan umum
                                 Swal.fire({
                                     icon: 'error',
                                     title: 'Error',
                                     text: 'Kata Sandi Lama Tidak Cocok.',
                                 });
                             }
                         }

                     });
                 });
             });
         </script>
