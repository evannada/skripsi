@extends('layouts.master')

@section('content')

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2">
        <div class="list-group">
          <a href="{{ route('home')}}" class="list-group-item list-group-item-action">
            <i class="far fa-user-circle"></i> <b>Dashboard</b>
          </a>
          <a href="{{ route('data-siswa.index') }}" class="list-group-item list-group-item-action"><i class="fas fa-list-alt"></i> <b>Data Siswa</b></a>
          <a href="{{ route('data-guru.index') }}" class="list-group-item list-group-item-action active"><i class="fas fa-list-alt"></i> <b>Data Guru</b></a>
          <a href="{{ route('data-mapel.index') }}" class="list-group-item list-group-item-action"> <i class="fas fa-list-alt"></i> <b>Data Mapel</b></a>
        </div>
      </div>

      <div class="col-md-9">
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">
            <h4>Data Guru
              <a onclick="addForm()" class="btn btn-primary pull-right" style="margin-top: -9px;"><i class="fas fa-plus"></i> Tambah </a>
            </h4>
          </div>
          <div class="panel-body">
          <!-- Table -->
          <table id="teacher-table" class="table table-striped">
            <thead>
              <tr>
                <th width="30">No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>NIG/NIP</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      </div>
      </div>
    </div>
@endsection

@include('users/form-teacher')

@include('users/form-mapel')

@section('script')

    <script type="text/javascript">
      var table = $('#teacher-table').DataTable({
                      processing: true,
                      serverSide: true,
                      ajax: "{{ route('api.data-guru') }}",
                      columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'username', name: 'username'},
                        {data: 'nig', name: 'nig'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                      ]
                    });

                    function addForm() {
                      save_method = "add";
                      $('input[name=_method]').val('POST');
                      $('#modal-form').modal('show');
                      $('#modal-form form')[0].reset();
                      $('.modal-title').text('Tambah Data');
                    }

                    function editForm(id) {
                      save_method = 'edit';
                      $('input[name=_method]').val('PATCH');
                      $('#modal-form form')[0].reset();
                      $.ajax({
                        url: "{{ url('data-guru') }}" + '/' + id + "/edit",
                        type: "GET",
                        dataType: "JSON",
                        success: function(data) {
                          $('#modal-form').modal('show');
                          $('.modal-title').text('Edit Data');

                          $('#id').val(data.id);
                          $('#name').val(data.name);
                          $('#username').val(data.username);
                          $('#nig').val(data.nig);
                        },
                        error : function() {
                            alert("Nothing Data");
                        }
                      });
                    }

                    function deleteData(id){
                        var csrf_token = $('meta[name="csrf-token"]').attr('content');
                        swal({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            type: 'warning',
                            showCancelButton: true,
                            cancelButtonColor: '#d33',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete it!'
                        }).then(function () {
                            $.ajax({
                                url : "{{ url('data-guru') }}" + '/' + id,
                                type : "POST",
                                data : {'_method' : 'DELETE', '_token' : csrf_token},
                                success : function(data) {
                                    table.ajax.reload();
                                    swal({
                                        title: 'Success!',
                                        text: data.message,
                                        type: 'success',
                                        timer: '1500'
                                    })
                                },
                                error : function () {
                                    swal({
                                        title: 'Oops...',
                                        text: data.message,
                                        type: 'error',
                                        timer: '1500'
                                    })
                                }
                            });
                        });
                      }

                    function mapel() {
                        $.ajax({
                          url: "{{ route('api.data-mapel') }}",
                          type: "GET",
                          dataType: "JSON",
                        success: function(data) {
                          $('#item-mapel').empty();
                          $.each(data.data, function(key, item){
                            $('#item-mapel')
                            .append('<input type="checkbox" name="'+item.name+'" value="'+item.id+'"><label>'+item.name+'</label>');

                          });

                          $('#modal-form-mapel').modal('show');
                          $('.modal-title').text('Setting Mata Kuliah');
                        },
                        error : function() {
                            alert("Nothing Data");
                        }
                      });
                    }


                    $(function(){
                          $('#modal-form form').validator().on('submit', function (e) {
                              if (!e.isDefaultPrevented()){
                                  var id = $('#id').val();
                                  if (save_method == 'add') url = "{{ url('data-guru') }}";
                                  else url = "{{ url('data-guru') . '/' }}" + id;

                                  $.ajax({
                                      url : url,
                                      type : "POST",
                                      data : $('#modal-form form').serialize(),
                                      success : function(data) {
                                          $('#modal-form').modal('hide');
                                          table.ajax.reload();
                                          swal({
                                              title: 'Success!',
                                              text: data.message,
                                              type: 'success',
                                              timer: '1500'
                                          })
                                      },
                                      error : function(data){
                                          swal({
                                              title: 'Opps...',
                                              text: data.responseJSON.message,
                                              type: 'error',
                                              timer: '2000'
                                          })
                                      }
                                  });
                                  return false;
                              }
                          });
                      });

      </script>
@endsection