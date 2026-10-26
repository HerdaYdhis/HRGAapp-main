<div>
    <style>
        /* CSS */
        #preview {
            transform: scaleX(1);
        }
    </style>
    <x-layouts.card>
        <x-slot:header>
            <div class="d-flex justify-content-between">
                <div class="row">
                    <h5 class="card-title"> Barcode Scan Canteen</h5>
                    <p>Please, Scan your barcode before eat. ~Thanks a Bunch</p>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div class="row">
                    <h3 id="countdown" style="display: none;">Scanning</h3>
                </div>
            </div>
        </x-slot:header>
        <x-slot:body>
            <x-alert.notification />
            <div class="row rounded-5 p-2 shadow">
                <div class="col-md-6 mb-md-0 mb-4">
                    <div class="py-2">
                        <video class="rounded-4 img-fluid" wire:model="preview" id="preview"
                            style="-webkit-transform: scaleX(-1); transform: scaleX(-1);"></video>
                    </div>
                </div>
                <div class="col-md-6 mb-md-0 mb-4">
                    <div class="pb-2 pt-2">
                        <h3>Name : {{ $name }}</h3>
                        <h3>Eat Status : {{ $eat }}</h3>
                        <h3>Department : {{ $department }}</h3>
                        <h3>Shift : {{ $shift }}</h3>
                        <h3>Employee Id : {{ $employee }}</h3>
                    </div>
                </div>
                <div class="pb-3"> <label for="cameraSelect" class="form-label">Select Camera:</label>
                    <select class="form-select" id="cameraSelect" onchange="changeCamera()">
                    </select>
                </div>
            </div>


            <div class="row rounded-2 border-3 py-4">
                <div class="card-toolbar">
                    @if (auth()->user()->is_superadmin)
                        <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                            Bulk Shift
                        </button>
                        <button type="button" wire:click="DeleteModal" class="btn btn-danger me-2">
                            <i class="bi bi-x"></i> Delete Bulk
                        </button>
                    @endif
                </div>
                <div class="rounded-2 py-4">
                    <livewire:canteen.datatable />
                </div>
            </div>
        </x-slot:body>
    </x-layouts.card>


    <x-modal.centered _id="{{ $modal }}" title="{{ $is_delete ? 'Delete' : 'Alert Validation' }}" form="submit">
        <x-slot:content>
            @if ($is_delete)
                <div class="text-bold text-danger p-3 text-xl">
                    Are Sure Delete Bulk on Schedule?
                </div>
            @else
                <div class="text-bold text-danger p-3 text-xl">
                    Are Sure a Bulk on Schedule?
                </div>
            @endif
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary ml-1" />
        </x-slot:action>
    </x-modal.centered>

</div>
@push('script')
    {{-- <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script> --}}
    <script src="/js/instascan.min.js"></script>
    {{-- <script type="text/javascript">
        let successSpeech = new Audio();
        let scannedData = '';
        let selectedCameraIndex = 0;
        let scanner = new Instascan.Scanner({
            video: document.getElementById('preview'),
            mirror: false,
        });

        scanner.addListener('scan', function(employee_id) {
            scannedData = employee_id;
            Livewire.emit('scanEmployee', scannedData);
        });

        Livewire.on('scanEmployeeResult', function(response) {
            console.log(response);
            if (response.success) {
                speakText(response.success);
            } else {
                speakText(response.error);
            }
            resetTransform();
        });

        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                var cameraSelect = document.getElementById('cameraSelect');
                cameras.forEach(function(camera, index) {
                    var option = document.createElement('option');
                    option.value = index;
                    option.text = camera.name || 'Camera ' + index;
                    cameraSelect.add(option);
                });

                var selectedCamera = cameras[0];

                window.changeCamera = function() {
                    var selectedIndex = cameraSelect.value;
                    selectedCamera = cameras[selectedIndex];
                    scanner.start(selectedCamera);
                };

                scanner.start(selectedCamera);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });

        function speakText(text) {

            // Menggunakan SpeechSynthesisUtterance untuk konfigurasi teks
            var utterance = new SpeechSynthesisUtterance(text);

            utterance.lang = 'id-ID';

            // Menetapkan elemen audio sebagai output suara
            utterance.addEventListener('end', function() {
                successSpeech.src = URL.createObjectURL(new Blob([], {
                    type: 'audio/wav'
                }));
            });

            // Membuat suara menggunakan sintesis suara
            successSpeech.src = URL.createObjectURL(new Blob([], {
                type: 'audio/wav'
            }));
            successSpeech.play();
            speechSynthesis.speak(utterance);
        }

        function resetTransform() {
            var previewElement = document.getElementById('preview');
            previewElement.style.transform = 'scaleX(1)';
        }
    </script> --}}
    <script type="text/javascript">
        let successSpeech = new Audio();
        let scannedData = '';
        let selectedCameraIndex = 0;
        let scanner = new Instascan.Scanner({
            video: document.getElementById('preview'),
            mirror: false,
        });

        let canScan = true; // Variable to control scanning status

        scanner.addListener('scan', function(employee_id) {
            if (canScan) {
                canScan = false; // Disable scanning
                scannedData = employee_id;
                Livewire.emit('scanEmployee', scannedData);

                // Show countdown
                startCountdown(3, function() {
                    canScan = true;
                });
            }
        });

        Livewire.on('scanEmployeeResult', function(response) {
            console.log(response);
            if (response.success) {
                speakText(response.success);
            } else {
                speakText(response.error);
            }
            resetTransform();
        });

        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                var cameraSelect = document.getElementById('cameraSelect');
                cameras.forEach(function(camera, index) {
                    var option = document.createElement('option');
                    option.value = index;
                    option.text = camera.name || 'Camera ' + index;
                    cameraSelect.add(option);
                });

                var selectedCamera = cameras[0];

                window.changeCamera = function() {
                    var selectedIndex = cameraSelect.value;
                    selectedCamera = cameras[selectedIndex];
                    scanner.start(selectedCamera);
                };

                scanner.start(selectedCamera);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });

        function speakText(text) {
            var utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';

            utterance.addEventListener('end', function() {
                successSpeech.src = URL.createObjectURL(new Blob([], {
                    type: 'audio/wav'
                }));
            });

            successSpeech.src = URL.createObjectURL(new Blob([], {
                type: 'audio/wav'
            }));
            successSpeech.play();
            speechSynthesis.speak(utterance);
        }

        function resetTransform() {
            var previewElement = document.getElementById('preview');
            previewElement.style.transform = 'scaleX(1)';
        }

        function startCountdown(seconds, callback) {
            let countdownElement = document.getElementById('countdown');
            countdownElement.style.display = 'block';
            countdownElement.innerText = 'Scanning';

            let interval = setInterval(() => {
                seconds--;
                countdownElement.innerText = 'Processing';

                if (seconds <= 0) {
                    clearInterval(interval);
                    countdownElement.style.display = 'none';
                    callback();
                }
            }, 1000);
        }
    </script>
@endpush
