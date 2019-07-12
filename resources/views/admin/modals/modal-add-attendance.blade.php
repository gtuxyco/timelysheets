<div class="ui modal add medium">
    <div class="header">Add New Attendance</div>
    <div class="content">
        <form id="add_attendance_form" action="{{ url('attendance/add') }}" class="ui form" method="post" accept-charset="utf-8">
            {{ csrf_field() }}
            <div class="field">
                <label>Employee</label>
                <select class="ui search dropdown getid uppercase" name="employee">
                    <option value="">Select Employee</option>
                    @isset($employee)
                    @foreach ($employee as $e)
                        <option value="{{ $e->lastname }}, {{ $e->firstname }}" data-id="{{ $e->id }}">{{ $e->lastname }}, {{ $e->firstname }}</option>
                    @endforeach
                    @endisset
                </select>
            </div>

            <div class="two fields">
                <div class="field">
                    <label for="">Time In</label>
                    <input type="text" placeholder="00:00:00 AM" name="timein" class="jtimepicker" />
                </div>
                <div class="field">
                    <label for="">Time In Date</label>
                    <input type="text" placeholder="Date" name="timein_date" class="airdatepicker" />
                </div>
            </div>

            <div class="two fields">
                <div class="field">
                    <label for="">Time Out</label>
                    <input type="text" placeholder="00:00:00 AM" name="timeout" class="jtimepicker" />
                </div>
                <div class="field">
                    <label for="">Time Out Date</label>
                    <input type="text" placeholder="Date" name="timeout_date" class="airdatepicker" />
                </div>
            </div>
            <div class="fields">
                        <div class="sixteen wide field">
                            <label>Reason</label>
                            <textarea class="" rows="5" name="reason"></textarea>
                        </div>
            </div>
           <div class="grouped fields field">
                <div class="ui error message">
                    <i class="close icon"></i>
                    <div class="header"></div>
                    <ul class="list">
                        <li class=""></li>
                    </ul>
                </div>
            </div>
        </div>
            
        <div class="actions">
            <input type="hidden" name="id" value="">
            <button class="ui positive small button" type="submit" name="submit"><i class="ui checkmark icon"></i> Save</button>
            <button class="ui grey small button cancel" type="button"><i class="ui times icon"></i> Cancel</button>
        </div>
        </form>  
</div>
