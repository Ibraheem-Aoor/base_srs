<!-- exam mark Modal -->
<div class="modal fade" id="mark_dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Exam Mark</h4>
          {{-- <a id="mark_dialog_join_button" class="btn btn-warning" style="color: #000;" href="#" target="_blank">Join Meeting</a> --}}
        </div>
        <div class="modal-body">
            <input type="hidden" id="mark_dialog_student_enroll_id"/>
            <input type="hidden" id="mark_dialog_student_subject_id"/>

            <div class="input-group mb-3">
                <select id="mark_dialog_exam_type" class="form-control">
                    <option value=""></option>
                    <?php
                    if (@$examTypes)
                    foreach ($examTypes as $key => $value) {
                        echo '<option value="'.$value->id.'">'.$value->title.'</option>';
                    }
                    ?>
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" onclick="MarkDialog.addMark();">Add</button>
                </div>
            </div>

            <table id="mark_dialog_marks" class="table">
                <thead>
                    <th>Mark Type</th>
                    <th>Value</th>
                    <th></th>
                </thead>
                <tbody></tbody>
            </table>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" onclick="MarkDialog.save();"><i class="fas fa-check"></i> {{ __('btn_save') }}</button>
        </div>
      </div>
    </div>
</div>

<script>

class MarkDialog{

    static show(studentEnrollId, subjectId){

        // $("#mark_dialog").modal('show');

        var enroll = $("#mark_dialog_student_enroll_id");
        enroll.val('');

        var subject = $("#mark_dialog_student_subject_id");
        subject.val('');

        $('#mark_dialog_marks > tbody').empty();

        if (!studentEnrollId || !subjectId) return;

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "GET",
            url: "<?=url('')?>/admin/exam/grading/get?enroll="+studentEnrollId+"&subject="+subjectId,
            processData: false,
            contentType: false,
            success: function(response)
            {
                if (!response) return;

                /////////////////////////////////////////////////////////////////////

                enroll.val(studentEnrollId);
                subject.val(subjectId);

                response.forEach(mark => {
                    MarkDialog.addMark(mark.exam_type_id, mark.achieve_marks);
                });

                $("#mark_dialog").modal('show');

                /////////////////////////////////////////////////////////////////////
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            complete: function(){

            }
        });

    }

    static save(){

        var enroll = document.querySelector("#mark_dialog_student_enroll_id");
        if (!enroll.value) return;

        var subjectId = document.querySelector("#mark_dialog_student_subject_id");
        if (!subject.value) return;

        var data = {
            enroll: enroll.value,
            subject: subject.value,
            exams: [],
        };

        var exams = document.querySelectorAll('input[name="examType[]"]');
        var marks = document.querySelectorAll('input[name="mark[]"]');
        for (var i = 0; i < exams.length; i++) {
            data.exams.push({
                exam_type_id: exams[i].value,
                mark: marks[i].value
            });
        }

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "POST",
            url: "<?=url('')?>/admin/exam/grading/store",
            processData: false,
            contentType: false,
            data: JSON.stringify(data),
            success: function(response)
            {
                if (!response) return;

                /////////////////////////////////////////////////////////////////////

                $("#mark_dialog").modal('hide');
                if (response == true){window.location.reload();}

                /////////////////////////////////////////////////////////////////////

            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            complete: function(){

            }
        });

    }

    static deleteMark(studentEnrollId){

        if (!markId) return;

        $.ajax({
            headers: {'X-CSRF-TOKEN': '<?=csrf_token()?>'},
            type: "GET",
            url: "<?=url('')?>/admin/exam/grading/delete/"+studentEnrollId,
            processData: false,
            contentType: false,
            success: function(response)
            {
                if (!response) return;

                /////////////////////////////////////////////////////////////////////


                // $("#mark_dialog").modal('show');

                /////////////////////////////////////////////////////////////////////

            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            complete: function(){

            }
        });

    }

    static addMark(examTypeId, mark){
        var marksTable = $('#mark_dialog_marks > tbody');
        var examType = $('#mark_dialog_exam_type');

        var examTitle = '';
        if (examTypeId){
            var selectedExamType = examType.find('option[value="'+examTypeId+'"]');
            examTitle = selectedExamType.text();
        }
        else{
            var selectedExamType = examType.find('option:selected');
            examTypeId = selectedExamType.val();
            examTitle = selectedExamType.text();
        }

        var row = `<tr>
            <td>
                <input type="hidden" name="examType[]" value="${examTypeId}"/>${examTitle}
            </td>
            <td><input class="" name="mark[]" value="${mark}"/></td>
            <td><a href="javascript:void(0);" onclick="" class="text-danger">Delete</a></td>
            </tr>`;
        marksTable.append(row);
    }

}


</script>
