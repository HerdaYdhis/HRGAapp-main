
<div class="row">
    <div class="col-6">
        <div class="d-flex align-items-center">
            <svg class="bi text-success" width="20" height="20" fill="black" style="width:10px">
                <use xlink:href="{{ asset('assets/images/bootstrap-icons.svg#circle-fill') }}" />
            </svg>
            <h5 class="ms-3 mb-0">{{$name}}</h5>
        </div>
    </div>
    <div class="col-12">
        <div {{$chart}}></div>
    </div>

</div>
