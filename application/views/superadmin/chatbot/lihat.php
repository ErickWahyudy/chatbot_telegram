<?php $this->load->view('template/header'); ?>
<?= $this->session->flashdata('pesan') ?>

<div class="row">
    <div class="col-md-12 col-sm-12 ">
        <div class="x_panel">
            <div class="x_title">
                <a href="" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahchatbot"><i
                        class="fa fa-plus"></i>
                    Tambah</a>
        

                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Command</th>
                                        <th>File</th>
                                        <th>Type file</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <?php $no=1; foreach($data as $chatbot): ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $chatbot['command'] ?></td>
                                    <td>
                                        <!-- jika format file maka yang ditampilkan link dan jika gambar maka img -->
                                         <?php if($chatbot['response_type'] == 'jpg' || $chatbot['response_type'] == 'png'): ?>
                                            <?= $chatbot['response_data'] ? '<img src="'.base_url('themes/chatbot/'.$chatbot['response_data']).'" width="100px">' : '' ?>
                                        <?php else: ?>
                                            <?= $chatbot['response_data'] ? '<a href="'.base_url('themes/chatbot/'.$chatbot['response_data']).'" target="_blank">'.$chatbot['response_data'].'</a>' : '' ?>
                                        <?php endif; ?>

                                    </td>
                                    <td><?= $chatbot['response_type'] ?></td>
                                    <td>
                                        <a href="" class="btn btn-warning" data-toggle="modal"
                                            data-target="#edit<?= $chatbot['id_bot_commands'] ?>"><i class="fa fa-edit"></i>
                                            Edit</a>
                                    </td>
                                </tr>
                                <?php $no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /page content -->
        </div>
    </div>
</div>

<!-- modal tambah -->
<div class="modal fade" id="modalTambahchatbot" tabindex="-1" role="dialog" aria-labelledby="modalTambahchatbotLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahchatbotLabel">Tambah <?= $judul ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="" style="width:100%">
                    <form id="add" method="post" enctype="multipart/form-data"> <!-- Tambahkan enctype -->
                        <tr>
                            <td><label for="nama">Command:</label></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="command" id="command" class="form-control" autocomplete="off"
                                    required placeholder="command" value="/"></td>
                        </tr>
                        
                        <tr>
                            <td><label for="nama">File:</label></td>
                        </tr>
                        <tr>
                            <td><input type="file" name="foto" id="foto" class="form-control"
                                    autocomplete="off" required placeholder="file"></td>
                        </tr>

                        <tr>
                            <td><label for="nama">Type file:</label></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="response_type" id="response_type" class="form-control" required>
                                    <option value="">Pilih Type File</option>
                                    <option value="jpg">jpg</option>
                                    <option value="png">png</option>
                                    <option value="pdf">pdf</option>
                                    <option value="doc">doc</option>
                                    <option value="docx">docx</option>
                                    <option value="xls">xls</option>
                                    <option value="xlsx">xlsx</option>
                                    <option value="ppt">ppt</option>
                                    <option value="pptx">pptx</option>
                                    <option value="mp3">mp3</option>
                                    <option value="mp4">mp4</option>
                                    <option value="txt">txt</option>
                                    <option value="zip">zip</option>
                                    <option value="rar">rar</option>
                                    <option value="7z">7z</option>
                                </select>
                        </tr>
                        
                        <tr>
                            <td><br><input type="submit" name="kirim" value="Simpan" class="btn btn-success"></td>
                        </tr>
                    </form>
                </table>
            </div>

        </div>
    </div>
</div>


<!-- modal edit chatbot -->
<?php foreach($data as $chatbot): ?>
<div class="modal fade" id="edit<?= $chatbot['id_bot_commands'] ?>" tabindex="-1" role="dialog"
    aria-labelledby="modalEditchatbotLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title" id="modalEditchatbotLabel">Edit <?= $judul ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-danger">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="" style="width:100%">
                    <form id="edit" method="post">
                        <input type="hidden" name="id_bot_commands" value="<?= $chatbot['id_bot_commands'] ?>">
                        <tr>
                            <td><label for="nama">Nama chatbot:</label></td>
                        </tr>
                        <tr>
                            <td><input type="text" name="command" id="command" class="form-control" autocomplete="off"
                                    value="<?= $chatbot['command'] ?>" required></td>
                        </tr>     
                        
                        <tr>
                            <td><label for="nama">File:</label></td>
                        </tr>
                        <tr>
                            <td><input type="file" name="files" id="files" class="form-control"
                                    autocomplete="off" value="<?= $chatbot['response_data'] ?>" required></td>
                        </tr>

                        <tr>
                            <td><label for="nama">Type file:</label></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="response_type" id="response_type" class="form-control" required>
                                    <option value="">Pilih Type File</option>
                                    <option value=".jpg" <?= $chatbot['response_type'] == '.jpg' ? 'selected' : '' ?>>.jpg
                                    </option>
                                    <option value=".png" <?= $chatbot['response_type'] == '.png' ? 'selected' : '' ?>>.png
                                    </option>
                                    <option value=".pdf" <?= $chatbot['response_type'] == '.pdf' ? 'selected' : '' ?>>.pdf
                                    </option>
                                    <option value=".doc" <?= $chatbot['response_type'] == '.doc' ? 'selected' : '' ?>>.doc
                                    </option>
                                    <option value=".docx" <?= $chatbot['response_type'] == '.docx' ? 'selected' : '' ?>>
                                        .docx</option>
                                    <option value=".xls" <?= $chatbot['response_type'] == '.xls' ? 'selected' : '' ?>>.xls
                                    </option>
                                    <option value=".xlsx" <?= $chatbot['response_type'] == '.xlsx' ? 'selected' : '' ?>>
                                        .xlsx</option>
                                    <option value=".ppt" <?= $chatbot['response_type'] == '.ppt' ? 'selected' : '' ?>>.ppt
                                    </option>
                                    <option value=".pptx" <?= $chatbot['response_type'] == '.pptx' ? 'selected' : '' ?>>
                                        .pptx</option>
                                    <option value=".mp3" <?= $chatbot['response_type'] == '.mp3' ? 'selected' : '' ?>>.mp3
                                    </option>
                                    <option value=".mp4" <?= $chatbot['response_type'] == '.mp4' ? 'selected' : '' ?>>.mp4
                                    </option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <br><input type="submit" name="kirim" value="Simpan" class="btn btn-success">
                                <a href="javascript:void(0)" onclick="hapuschatbot('<?= $chatbot['id_bot_commands'] ?>')"
                                    class="btn btn-danger">Hapus</a>
                            </td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>


<script>
//add data
$(document).ready(function() {
    $('#add').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= site_url('superadmin/chatbot/api_add') ?>",
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            cache: false,
            async: false,
            success: function(data) {
                if (data.status) {
                    $('#modalTambahchatbot');
                    $('#add')[0].reset();
                    swal({
                        title: "Berhasil",
                        text: "Data berhasil ditambahkan",
                        type: "success",
                        showConfirmButton: true,
                        confirmButtonText: "OKEE",
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    // Hapus tag HTML dari pesan error
                    var errorMessage = $('<div>').html(data.message).text();
                    swal({
                        title: "Gagal",
                        text: errorMessage, // Menampilkan pesan error dari server
                        type: "error",
                        showConfirmButton: true,
                        confirmButtonText: "OK",
                    });
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Menampilkan pesan error jika terjadi kesalahan pada AJAX request
                swal({
                    title: "Error",
                    text: "Terjadi kesalahan saat mengirim data",
                    type: "error",
                    showConfirmButton: true,
                    confirmButtonText: "OK",
                });
            }
        });
    });
});

//edit file
$(document).on('submit', '#edit', function(e) {
    e.preventDefault();
    var form_data = new FormData(this);

    $.ajax({
        type: "POST",
        url: "<?php echo site_url('superadmin/chatbot/api_edit/') ?>" + form_data.get('id_bot_commands'),
        dataType: "json",
        data: form_data,
        processData: false,
        contentType: false,
        //memanggil swall ketika berhasil
        success: function(data) {
            $('#edit' + form_data.get('id_bot_commands'));
            swal({
                title: "Berhasil",
                text: "Data Berhasil Diubah",
                type: "success",
                showConfirmButton: true,
                confirmButtonText: "OKEE",
            }).then(function() {
                location.reload();
            });
        },
        //memanggil swall ketika gagal
        error: function(data) {
            swal({
                title: "Gagal",
                text: "Data Gagal Diubah",
                type: "error",
                showConfirmButton: true,
                confirmButtonText: "OKEE",
            }).then(function() {
                location.reload();
            });
        }
    });
});

//ajax hapus chatbot
function hapuschatbot(id_bot_commands) {
    swal({
        title: "Apakah Anda Yakin?",
        text: "Data Akan Dihapus",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Tidak, Batalkan!",
        closeOnConfirm: false,
        closeOnCancel: true // Set this to true to close the dialog when the cancel button is clicked
    }).then(function(result) {
        if (result.value) { // Only delete the data if the user clicked on the confirm button
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('superadmin/chatbot/api_hapus/') ?>" +
                    id_bot_commands,
                dataType: "json",
            }).done(function() {
                swal({
                    title: "Berhasil",
                    text: "Data Berhasil Dihapus",
                    type: "success",
                    showConfirmButton: true,
                    confirmButtonText: "OKEE"
                }).then(function() {
                    location.reload();
                });
            }).fail(function() {
                swal({
                    title: "Gagal",
                    text: "Data Gagal Dihapus",
                    type: "error",
                    showConfirmButton: true,
                    confirmButtonText: "OKEE"
                }).then(function() {
                    location.reload();
                });
            });
        } else { // If the user clicked on the cancel button, show a message indicating that the deletion was cancelled
            swal("Batal hapus", "Data Tidak Jadi Dihapus", "error");
        }
    });
}

</script>

<?php $this->load->view('template/footer'); ?>