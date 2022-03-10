@extends('layouts.app')
{{--@section('content')--}}
    {{--<app-component :contents="{{ json_encode($data) }}"></app-component>--}}
{{--@endsection--}}

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ route('app.index') }}" method="get">
                        <div class="card-header">
                            <h4>Administración de aplicación
                            @can('Cron.create')
                                <button type="button" class="btn btn-sm btn-outline-primary float-right" data-toggle="modal" data-target="#ModalCrear">Crear</button>
                            @endcan
                            </h4>
                        </div>

                        <div class="card-body table-responsive">
                            <div class="input-group">
                                <input type="search" name="search" id="search" class="form-control border-info" placeholder="Buscar">
                                <span class="input-group-prepend">
                                    <button type="submit" class="btn btn-outline-primary" id="seacrh">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </span>
                            </div> <br>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <i class="fab fa-facebook"></i>
                                        Aplicaciones de Facebook
                                    </div>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>Posición</th>
                                            <th>Nombre</th>
                                            <th>Aplicación</th>
                                            <th>Id de la APP FB</th>
                                            <th>Acciones</th>
                                            <th colspan="3">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($app as $apps)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $apps->name_app}}</td>
                                                <td>{{ $apps->app}}</td>
                                                <td>{{ $apps->app_fb_id}}</td>
                                                <td >
                                                    <div class="list-group-item-figure">
                                                        <button type="button" onclick="show({{ $apps->id }})" data-user="{{ $apps->id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                            <i class="icon-pencil"></i>
                                                        </button>
                                                        <a onclick="confirmation(event)" href="./App/delete/{{$apps->id}}/Facebook " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                            <i class="icon-close"></i>
                                                        </a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if(@isset($twitter))
                                <div class="card">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="fab fa-twitter"></i>
                                            Aplicaciones de Twitter
                                        </div>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                            <tr>
                                                <th>Posición</th>
                                                <th>Nombre</th>
                                                <th>Aplicación</th>
                                                <th>Clave del consumidor</th>
                                                <th>Acciones</th>
                                                <th colspan="3">&nbsp;</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($twitter as $apps)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $apps->name_app}}</td>
                                                    <td>Twitter</td>
                                                    <td>{{ $apps->consumer_key}}</td>
                                                    <td >
                                                        <div class="list-group-item-figure">
                                                            <button type="button" onclick="showTwitter({{ $apps->id }})" data-user="{{ $apps->id }}" class="btn btn-sm btn-icon btn-round btn-success mt-3" data-toggle="modal" data-target="#showModal">
                                                                <i class="icon-pencil"></i>
                                                            </button>
                                                            <a onclick="confirmation(event)" href="./App/delete/{{$apps->id}}/Twitter " class="btn btn-sm btn-icon btn-round btn-danger mt-3">
                                                                <i class="icon-close"></i>
                                                            </a>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            {{--{{ $app->appends($_GET)->links() }}--}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL CREAR ---------------------------------------------------}}

    <div class="modal fade" id="ModalCrear" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva aplicación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="name_app">Nombre</label>
                            <input type="text" class="form-control" id="name_app" placeholder="Ingrese un nombre de la aplicación...">
                        </div>

                        <div class="form-group">
                            {{ Form::label('apps','Seleccione la aplicación ') }}
                            {{ Form::select('apps', ['Facebook' => 'Facebook', 'Twitter' => 'Twitter', 'Linkedin' => 'Linkedin'], null, ['placeholder' => 'Seleccione un aplicación...','class' => 'form-control'])}}
                        </div>

                        <div class="form-group">
                            <div class="form-row align-items-center">
                                <div class="col-sm-12 my-1">
                                    <label for="app_fb_id">Ingrese número de teléfono</label>
                                </div>

                                <div class="col-sm-5">
                                    <select style="" class="form-control" id="country">
                                        <option data-countryCode="US" value="0" selected>Seleccione</option>
                                        <option data-countryCode="US" value="1" >USA (+1)</option>
                                        <option data-countryCode="GB" value="44">UK (+44)</option>
                                        <option data-countryCode="DZ" value="213">Algeria (+213)</option>
                                        <option data-countryCode="AD" value="376">Andorra (+376)</option>
                                        <option data-countryCode="AO" value="244">Angola (+244)</option>
                                        <option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
                                        <option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
                                        <option data-countryCode="AR" value="54">Argentina (+54)</option>
                                        <option data-countryCode="AM" value="374">Armenia (+374)</option>
                                        <option data-countryCode="AW" value="297">Aruba (+297)</option>
                                        <option data-countryCode="AU" value="61">Australia (+61)</option>
                                        <option data-countryCode="AT" value="43">Austria (+43)</option>
                                        <option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
                                        <option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
                                        <option data-countryCode="BH" value="973">Bahrain (+973)</option>
                                        <option data-countryCode="BD" value="880">Bangladesh (+880)</option>
                                        <option data-countryCode="BB" value="1246">Barbados (+1246)</option>
                                        <option data-countryCode="BY" value="375">Belarus (+375)</option>
                                        <option data-countryCode="BE" value="32">Belgium (+32)</option>
                                        <option data-countryCode="BZ" value="501">Belize (+501)</option>
                                        <option data-countryCode="BJ" value="229">Benin (+229)</option>
                                        <option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
                                        <option data-countryCode="BT" value="975">Bhutan (+975)</option>
                                        <option data-countryCode="BO" value="591">Bolivia (+591)</option>
                                        <option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
                                        <option data-countryCode="BW" value="267">Botswana (+267)</option>
                                        <option data-countryCode="BR" value="55">Brazil (+55)</option>
                                        <option data-countryCode="BN" value="673">Brunei (+673)</option>
                                        <option data-countryCode="BG" value="359">Bulgaria (+359)</option>
                                        <option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
                                        <option data-countryCode="BI" value="257">Burundi (+257)</option>
                                        <option data-countryCode="KH" value="855">Cambodia (+855)</option>
                                        <option data-countryCode="CM" value="237">Cameroon (+237)</option>
                                        <option data-countryCode="CA" value="1">Canada (+1)</option>
                                        <option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
                                        <option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
                                        <option data-countryCode="CF" value="236">Central African Republic (+236)</option>
                                        <option data-countryCode="CL" value="56">Chile (+56)</option>
                                        <option data-countryCode="CN" value="86">China (+86)</option>
                                        <option data-countryCode="CO" value="57">Colombia (+57)</option>
                                        <option data-countryCode="KM" value="269">Comoros (+269)</option>
                                        <option data-countryCode="CG" value="242">Congo (+242)</option>
                                        <option data-countryCode="CK" value="682">Cook Islands (+682)</option>
                                        <option data-countryCode="CR" value="506">Costa Rica (+506)</option>
                                        <option data-countryCode="HR" value="385">Croatia (+385)</option>
                                        <!-- <option data-countryCode="CU" value="53">Cuba (+53)</option> -->
                                        <option data-countryCode="CY" value="90">Cyprus - North (+90)</option>
                                        <option data-countryCode="CY" value="357">Cyprus - South (+357)</option>
                                        <option data-countryCode="CZ" value="420">Czech Republic (+420)</option>
                                        <option data-countryCode="DK" value="45">Denmark (+45)</option>
                                        <option data-countryCode="DJ" value="253">Djibouti (+253)</option>
                                        <option data-countryCode="DM" value="1809">Dominica (+1809)</option>
                                        <option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
                                        <option data-countryCode="EC" value="593">Ecuador (+593)</option>
                                        <option data-countryCode="EG" value="20">Egypt (+20)</option>
                                        <option data-countryCode="SV" value="503">El Salvador (+503)</option>
                                        <option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
                                        <option data-countryCode="ER" value="291">Eritrea (+291)</option>
                                        <option data-countryCode="EE" value="372">Estonia (+372)</option>
                                        <option data-countryCode="ET" value="251">Ethiopia (+251)</option>
                                        <option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
                                        <option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
                                        <option data-countryCode="FJ" value="679">Fiji (+679)</option>
                                        <option data-countryCode="FI" value="358">Finland (+358)</option>
                                        <option data-countryCode="FR" value="33">France (+33)</option>
                                        <option data-countryCode="GF" value="594">French Guiana (+594)</option>
                                        <option data-countryCode="PF" value="689">French Polynesia (+689)</option>
                                        <option data-countryCode="GA" value="241">Gabon (+241)</option>
                                        <option data-countryCode="GM" value="220">Gambia (+220)</option>
                                        <option data-countryCode="GE" value="7880">Georgia (+7880)</option>
                                        <option data-countryCode="DE" value="49">Germany (+49)</option>
                                        <option data-countryCode="GH" value="233">Ghana (+233)</option>
                                        <option data-countryCode="GI" value="350">Gibraltar (+350)</option>
                                        <option data-countryCode="GR" value="30">Greece (+30)</option>
                                        <option data-countryCode="GL" value="299">Greenland (+299)</option>
                                        <option data-countryCode="GD" value="1473">Grenada (+1473)</option>
                                        <option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
                                        <option data-countryCode="GU" value="671">Guam (+671)</option>
                                        <option data-countryCode="GT" value="502">Guatemala (+502)</option>
                                        <option data-countryCode="GN" value="224">Guinea (+224)</option>
                                        <option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
                                        <option data-countryCode="GY" value="592">Guyana (+592)</option>
                                        <option data-countryCode="HT" value="509">Haiti (+509)</option>
                                        <option data-countryCode="HN" value="504">Honduras (+504)</option>
                                        <option data-countryCode="HK" value="852">Hong Kong (+852)</option>
                                        <option data-countryCode="HU" value="36">Hungary (+36)</option>
                                        <option data-countryCode="IS" value="354">Iceland (+354)</option>
                                        <option data-countryCode="IN" value="91">India (+91)</option>
                                        <option data-countryCode="ID" value="62">Indonesia (+62)</option>
                                        <option data-countryCode="IQ" value="964">Iraq (+964)</option>
                                        <!-- <option data-countryCode="IR" value="98">Iran (+98)</option> -->
                                        <option data-countryCode="IE" value="353">Ireland (+353)</option>
                                        <option data-countryCode="IL" value="972">Israel (+972)</option>
                                        <option data-countryCode="IT" value="39">Italy (+39)</option>
                                        <option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
                                        <option data-countryCode="JP" value="81">Japan (+81)</option>
                                        <option data-countryCode="JO" value="962">Jordan (+962)</option>
                                        <option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
                                        <option data-countryCode="KE" value="254">Kenya (+254)</option>
                                        <option data-countryCode="KI" value="686">Kiribati (+686)</option>
                                        <!-- <option data-countryCode="KP" value="850">Korea - North (+850)</option> -->
                                        <option data-countryCode="KR" value="82">Korea - South (+82)</option>
                                        <option data-countryCode="KW" value="965">Kuwait (+965)</option>
                                        <option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
                                        <option data-countryCode="LA" value="856">Laos (+856)</option>
                                        <option data-countryCode="LV" value="371">Latvia (+371)</option>
                                        <option data-countryCode="LB" value="961">Lebanon (+961)</option>
                                        <option data-countryCode="LS" value="266">Lesotho (+266)</option>
                                        <option data-countryCode="LR" value="231">Liberia (+231)</option>
                                        <option data-countryCode="LY" value="218">Libya (+218)</option>
                                        <option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
                                        <option data-countryCode="LT" value="370">Lithuania (+370)</option>
                                        <option data-countryCode="LU" value="352">Luxembourg (+352)</option>
                                        <option data-countryCode="MO" value="853">Macao (+853)</option>
                                        <option data-countryCode="MK" value="389">Macedonia (+389)</option>
                                        <option data-countryCode="MG" value="261">Madagascar (+261)</option>
                                        <option data-countryCode="MW" value="265">Malawi (+265)</option>
                                        <option data-countryCode="MY" value="60">Malaysia (+60)</option>
                                        <option data-countryCode="MV" value="960">Maldives (+960)</option>
                                        <option data-countryCode="ML" value="223">Mali (+223)</option>
                                        <option data-countryCode="MT" value="356">Malta (+356)</option>
                                        <option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
                                        <option data-countryCode="MQ" value="596">Martinique (+596)</option>
                                        <option data-countryCode="MR" value="222">Mauritania (+222)</option>
                                        <option data-countryCode="YT" value="269">Mayotte (+269)</option>
                                        <option data-countryCode="MX" value="52">Mexico (+52)</option>
                                        <option data-countryCode="FM" value="691">Micronesia (+691)</option>
                                        <option data-countryCode="MD" value="373">Moldova (+373)</option>
                                        <option data-countryCode="MC" value="377">Monaco (+377)</option>
                                        <option data-countryCode="MN" value="976">Mongolia (+976)</option>
                                        <option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
                                        <option data-countryCode="MA" value="212">Morocco (+212)</option>
                                        <option data-countryCode="MZ" value="258">Mozambique (+258)</option>
                                        <option data-countryCode="MN" value="95">Myanmar (+95)</option>
                                        <option data-countryCode="NA" value="264">Namibia (+264)</option>
                                        <option data-countryCode="NR" value="674">Nauru (+674)</option>
                                        <option data-countryCode="NP" value="977">Nepal (+977)</option>
                                        <option data-countryCode="NL" value="31">Netherlands (+31)</option>
                                        <option data-countryCode="NC" value="687">New Caledonia (+687)</option>
                                        <option data-countryCode="NZ" value="64">New Zealand (+64)</option>
                                        <option data-countryCode="NI" value="505">Nicaragua (+505)</option>
                                        <option data-countryCode="NE" value="227">Niger (+227)</option>
                                        <option data-countryCode="NG" value="234">Nigeria (+234)</option>
                                        <option data-countryCode="NU" value="683">Niue (+683)</option>
                                        <option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
                                        <option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
                                        <option data-countryCode="NO" value="47">Norway (+47)</option>
                                        <option data-countryCode="OM" value="968">Oman (+968)</option>
                                        <option data-countryCode="PK" value="92">Pakistan (+92)</option>
                                        <option data-countryCode="PW" value="680">Palau (+680)</option>
                                        <option data-countryCode="PA" value="507">Panama (+507)</option>
                                        <option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
                                        <option data-countryCode="PY" value="595">Paraguay (+595)</option>
                                        <option data-countryCode="PE" value="51">Peru (+51)</option>
                                        <option data-countryCode="PH" value="63">Philippines (+63)</option>
                                        <option data-countryCode="PL" value="48">Poland (+48)</option>
                                        <option data-countryCode="PT" value="351">Portugal (+351)</option>
                                        <option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
                                        <option data-countryCode="QA" value="974">Qatar (+974)</option>
                                        <option data-countryCode="RE" value="262">Reunion (+262)</option>
                                        <option data-countryCode="RO" value="40">Romania (+40)</option>
                                        <option data-countryCode="RU" value="7">Russia (+7)</option>
                                        <option data-countryCode="RW" value="250">Rwanda (+250)</option>
                                        <option data-countryCode="SM" value="378">San Marino (+378)</option>
                                        <option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
                                        <option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
                                        <option data-countryCode="SN" value="221">Senegal (+221)</option>
                                        <option data-countryCode="CS" value="381">Serbia (+381)</option>
                                        <option data-countryCode="SC" value="248">Seychelles (+248)</option>
                                        <option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
                                        <option data-countryCode="SG" value="65">Singapore (+65)</option>
                                        <option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
                                        <option data-countryCode="SI" value="386">Slovenia (+386)</option>
                                        <option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
                                        <option data-countryCode="SO" value="252">Somalia (+252)</option>
                                        <option data-countryCode="ZA" value="27">South Africa (+27)</option>
                                        <option data-countryCode="ES" value="34">Spain (+34)</option>
                                        <option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
                                        <option data-countryCode="SH" value="290">St. Helena (+290)</option>
                                        <option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
                                        <option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
                                        <option data-countryCode="SR" value="597">Suriname (+597)</option>
                                        <option data-countryCode="SD" value="249">Sudan (+249)</option>
                                        <option data-countryCode="SZ" value="268">Swaziland (+268)</option>
                                        <option data-countryCode="SE" value="46">Sweden (+46)</option>
                                        <option data-countryCode="CH" value="41">Switzerland (+41)</option>
                                        <!-- <option data-countryCode="SY" value="963">Syria (+963)</option> -->
                                        <option data-countryCode="TW" value="886">Taiwan (+886)</option>
                                        <option data-countryCode="TJ" value="992">Tajikistan (+992)</option>
                                        <option data-countryCode="TH" value="66">Thailand (+66)</option>
                                        <option data-countryCode="TG" value="228">Togo (+228)</option>
                                        <option data-countryCode="TO" value="676">Tonga (+676)</option>
                                        <option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
                                        <option data-countryCode="TN" value="216">Tunisia (+216)</option>
                                        <option data-countryCode="TR" value="90">Turkey (+90)</option>
                                        <option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
                                        <option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
                                        <option data-countryCode="TV" value="688">Tuvalu (+688)</option>
                                        <option data-countryCode="UG" value="256">Uganda (+256)</option>
                                        <option data-countryCode="UA" value="380">Ukraine (+380)</option>
                                        <option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
                                        <option data-countryCode="UY" value="598">Uruguay (+598)</option>
                                        <option data-countryCode="UZ" value="998">Uzbekistan (+998)</option>
                                        <option data-countryCode="VU" value="678">Vanuatu (+678)</option>
                                        <option data-countryCode="VA" value="379">Vatican City (+379)</option>
                                        <option data-countryCode="VE" value="58">Venezuela (+58)</option>
                                        <option data-countryCode="VN" value="84">Vietnam (+84)</option>
                                        <option data-countryCode="VG" value="1">Virgin Islands - British (+1)</option>
                                        <option data-countryCode="VI" value="1">Virgin Islands - US (+1)</option>
                                        <option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
                                        <option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
                                        <option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
                                        <option data-countryCode="ZM" value="260">Zambia (+260)</option>
                                        <option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
                                    </select>
                                </div>

                                <div class="col-sm-7 my-1">
                                    <input style=""  type="text" class="form-control" id="number" placeholder="00000000">
                                </div>

                                <div class="col-sm-5">
                                    <select style="" class="form-control" id="country_num">
                                        <option data-countryCode="US" value="0" selected>Seleccione</option>
                                        <option data-countryCode="US" value="1" >USA (+1)</option>
                                        <option data-countryCode="GB" value="44">UK (+44)</option>
                                        <option data-countryCode="DZ" value="213">Algeria (+213)</option>
                                        <option data-countryCode="AD" value="376">Andorra (+376)</option>
                                        <option data-countryCode="AO" value="244">Angola (+244)</option>
                                        <option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
                                        <option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
                                        <option data-countryCode="AR" value="54">Argentina (+54)</option>
                                        <option data-countryCode="AM" value="374">Armenia (+374)</option>
                                        <option data-countryCode="AW" value="297">Aruba (+297)</option>
                                        <option data-countryCode="AU" value="61">Australia (+61)</option>
                                        <option data-countryCode="AT" value="43">Austria (+43)</option>
                                        <option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
                                        <option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
                                        <option data-countryCode="BH" value="973">Bahrain (+973)</option>
                                        <option data-countryCode="BD" value="880">Bangladesh (+880)</option>
                                        <option data-countryCode="BB" value="1246">Barbados (+1246)</option>
                                        <option data-countryCode="BY" value="375">Belarus (+375)</option>
                                        <option data-countryCode="BE" value="32">Belgium (+32)</option>
                                        <option data-countryCode="BZ" value="501">Belize (+501)</option>
                                        <option data-countryCode="BJ" value="229">Benin (+229)</option>
                                        <option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
                                        <option data-countryCode="BT" value="975">Bhutan (+975)</option>
                                        <option data-countryCode="BO" value="591">Bolivia (+591)</option>
                                        <option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
                                        <option data-countryCode="BW" value="267">Botswana (+267)</option>
                                        <option data-countryCode="BR" value="55">Brazil (+55)</option>
                                        <option data-countryCode="BN" value="673">Brunei (+673)</option>
                                        <option data-countryCode="BG" value="359">Bulgaria (+359)</option>
                                        <option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
                                        <option data-countryCode="BI" value="257">Burundi (+257)</option>
                                        <option data-countryCode="KH" value="855">Cambodia (+855)</option>
                                        <option data-countryCode="CM" value="237">Cameroon (+237)</option>
                                        <option data-countryCode="CA" value="1">Canada (+1)</option>
                                        <option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
                                        <option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
                                        <option data-countryCode="CF" value="236">Central African Republic (+236)</option>
                                        <option data-countryCode="CL" value="56">Chile (+56)</option>
                                        <option data-countryCode="CN" value="86">China (+86)</option>
                                        <option data-countryCode="CO" value="57">Colombia (+57)</option>
                                        <option data-countryCode="KM" value="269">Comoros (+269)</option>
                                        <option data-countryCode="CG" value="242">Congo (+242)</option>
                                        <option data-countryCode="CK" value="682">Cook Islands (+682)</option>
                                        <option data-countryCode="CR" value="506">Costa Rica (+506)</option>
                                        <option data-countryCode="HR" value="385">Croatia (+385)</option>
                                        <!-- <option data-countryCode="CU" value="53">Cuba (+53)</option> -->
                                        <option data-countryCode="CY" value="90">Cyprus - North (+90)</option>
                                        <option data-countryCode="CY" value="357">Cyprus - South (+357)</option>
                                        <option data-countryCode="CZ" value="420">Czech Republic (+420)</option>
                                        <option data-countryCode="DK" value="45">Denmark (+45)</option>
                                        <option data-countryCode="DJ" value="253">Djibouti (+253)</option>
                                        <option data-countryCode="DM" value="1809">Dominica (+1809)</option>
                                        <option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
                                        <option data-countryCode="EC" value="593">Ecuador (+593)</option>
                                        <option data-countryCode="EG" value="20">Egypt (+20)</option>
                                        <option data-countryCode="SV" value="503">El Salvador (+503)</option>
                                        <option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
                                        <option data-countryCode="ER" value="291">Eritrea (+291)</option>
                                        <option data-countryCode="EE" value="372">Estonia (+372)</option>
                                        <option data-countryCode="ET" value="251">Ethiopia (+251)</option>
                                        <option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
                                        <option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
                                        <option data-countryCode="FJ" value="679">Fiji (+679)</option>
                                        <option data-countryCode="FI" value="358">Finland (+358)</option>
                                        <option data-countryCode="FR" value="33">France (+33)</option>
                                        <option data-countryCode="GF" value="594">French Guiana (+594)</option>
                                        <option data-countryCode="PF" value="689">French Polynesia (+689)</option>
                                        <option data-countryCode="GA" value="241">Gabon (+241)</option>
                                        <option data-countryCode="GM" value="220">Gambia (+220)</option>
                                        <option data-countryCode="GE" value="7880">Georgia (+7880)</option>
                                        <option data-countryCode="DE" value="49">Germany (+49)</option>
                                        <option data-countryCode="GH" value="233">Ghana (+233)</option>
                                        <option data-countryCode="GI" value="350">Gibraltar (+350)</option>
                                        <option data-countryCode="GR" value="30">Greece (+30)</option>
                                        <option data-countryCode="GL" value="299">Greenland (+299)</option>
                                        <option data-countryCode="GD" value="1473">Grenada (+1473)</option>
                                        <option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
                                        <option data-countryCode="GU" value="671">Guam (+671)</option>
                                        <option data-countryCode="GT" value="502">Guatemala (+502)</option>
                                        <option data-countryCode="GN" value="224">Guinea (+224)</option>
                                        <option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
                                        <option data-countryCode="GY" value="592">Guyana (+592)</option>
                                        <option data-countryCode="HT" value="509">Haiti (+509)</option>
                                        <option data-countryCode="HN" value="504">Honduras (+504)</option>
                                        <option data-countryCode="HK" value="852">Hong Kong (+852)</option>
                                        <option data-countryCode="HU" value="36">Hungary (+36)</option>
                                        <option data-countryCode="IS" value="354">Iceland (+354)</option>
                                        <option data-countryCode="IN" value="91">India (+91)</option>
                                        <option data-countryCode="ID" value="62">Indonesia (+62)</option>
                                        <option data-countryCode="IQ" value="964">Iraq (+964)</option>
                                        <!-- <option data-countryCode="IR" value="98">Iran (+98)</option> -->
                                        <option data-countryCode="IE" value="353">Ireland (+353)</option>
                                        <option data-countryCode="IL" value="972">Israel (+972)</option>
                                        <option data-countryCode="IT" value="39">Italy (+39)</option>
                                        <option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
                                        <option data-countryCode="JP" value="81">Japan (+81)</option>
                                        <option data-countryCode="JO" value="962">Jordan (+962)</option>
                                        <option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
                                        <option data-countryCode="KE" value="254">Kenya (+254)</option>
                                        <option data-countryCode="KI" value="686">Kiribati (+686)</option>
                                        <!-- <option data-countryCode="KP" value="850">Korea - North (+850)</option> -->
                                        <option data-countryCode="KR" value="82">Korea - South (+82)</option>
                                        <option data-countryCode="KW" value="965">Kuwait (+965)</option>
                                        <option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
                                        <option data-countryCode="LA" value="856">Laos (+856)</option>
                                        <option data-countryCode="LV" value="371">Latvia (+371)</option>
                                        <option data-countryCode="LB" value="961">Lebanon (+961)</option>
                                        <option data-countryCode="LS" value="266">Lesotho (+266)</option>
                                        <option data-countryCode="LR" value="231">Liberia (+231)</option>
                                        <option data-countryCode="LY" value="218">Libya (+218)</option>
                                        <option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
                                        <option data-countryCode="LT" value="370">Lithuania (+370)</option>
                                        <option data-countryCode="LU" value="352">Luxembourg (+352)</option>
                                        <option data-countryCode="MO" value="853">Macao (+853)</option>
                                        <option data-countryCode="MK" value="389">Macedonia (+389)</option>
                                        <option data-countryCode="MG" value="261">Madagascar (+261)</option>
                                        <option data-countryCode="MW" value="265">Malawi (+265)</option>
                                        <option data-countryCode="MY" value="60">Malaysia (+60)</option>
                                        <option data-countryCode="MV" value="960">Maldives (+960)</option>
                                        <option data-countryCode="ML" value="223">Mali (+223)</option>
                                        <option data-countryCode="MT" value="356">Malta (+356)</option>
                                        <option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
                                        <option data-countryCode="MQ" value="596">Martinique (+596)</option>
                                        <option data-countryCode="MR" value="222">Mauritania (+222)</option>
                                        <option data-countryCode="YT" value="269">Mayotte (+269)</option>
                                        <option data-countryCode="MX" value="52">Mexico (+52)</option>
                                        <option data-countryCode="FM" value="691">Micronesia (+691)</option>
                                        <option data-countryCode="MD" value="373">Moldova (+373)</option>
                                        <option data-countryCode="MC" value="377">Monaco (+377)</option>
                                        <option data-countryCode="MN" value="976">Mongolia (+976)</option>
                                        <option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
                                        <option data-countryCode="MA" value="212">Morocco (+212)</option>
                                        <option data-countryCode="MZ" value="258">Mozambique (+258)</option>
                                        <option data-countryCode="MN" value="95">Myanmar (+95)</option>
                                        <option data-countryCode="NA" value="264">Namibia (+264)</option>
                                        <option data-countryCode="NR" value="674">Nauru (+674)</option>
                                        <option data-countryCode="NP" value="977">Nepal (+977)</option>
                                        <option data-countryCode="NL" value="31">Netherlands (+31)</option>
                                        <option data-countryCode="NC" value="687">New Caledonia (+687)</option>
                                        <option data-countryCode="NZ" value="64">New Zealand (+64)</option>
                                        <option data-countryCode="NI" value="505">Nicaragua (+505)</option>
                                        <option data-countryCode="NE" value="227">Niger (+227)</option>
                                        <option data-countryCode="NG" value="234">Nigeria (+234)</option>
                                        <option data-countryCode="NU" value="683">Niue (+683)</option>
                                        <option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
                                        <option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
                                        <option data-countryCode="NO" value="47">Norway (+47)</option>
                                        <option data-countryCode="OM" value="968">Oman (+968)</option>
                                        <option data-countryCode="PK" value="92">Pakistan (+92)</option>
                                        <option data-countryCode="PW" value="680">Palau (+680)</option>
                                        <option data-countryCode="PA" value="507">Panama (+507)</option>
                                        <option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
                                        <option data-countryCode="PY" value="595">Paraguay (+595)</option>
                                        <option data-countryCode="PE" value="51">Peru (+51)</option>
                                        <option data-countryCode="PH" value="63">Philippines (+63)</option>
                                        <option data-countryCode="PL" value="48">Poland (+48)</option>
                                        <option data-countryCode="PT" value="351">Portugal (+351)</option>
                                        <option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
                                        <option data-countryCode="QA" value="974">Qatar (+974)</option>
                                        <option data-countryCode="RE" value="262">Reunion (+262)</option>
                                        <option data-countryCode="RO" value="40">Romania (+40)</option>
                                        <option data-countryCode="RU" value="7">Russia (+7)</option>
                                        <option data-countryCode="RW" value="250">Rwanda (+250)</option>
                                        <option data-countryCode="SM" value="378">San Marino (+378)</option>
                                        <option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
                                        <option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
                                        <option data-countryCode="SN" value="221">Senegal (+221)</option>
                                        <option data-countryCode="CS" value="381">Serbia (+381)</option>
                                        <option data-countryCode="SC" value="248">Seychelles (+248)</option>
                                        <option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
                                        <option data-countryCode="SG" value="65">Singapore (+65)</option>
                                        <option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
                                        <option data-countryCode="SI" value="386">Slovenia (+386)</option>
                                        <option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
                                        <option data-countryCode="SO" value="252">Somalia (+252)</option>
                                        <option data-countryCode="ZA" value="27">South Africa (+27)</option>
                                        <option data-countryCode="ES" value="34">Spain (+34)</option>
                                        <option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
                                        <option data-countryCode="SH" value="290">St. Helena (+290)</option>
                                        <option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
                                        <option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
                                        <option data-countryCode="SR" value="597">Suriname (+597)</option>
                                        <option data-countryCode="SD" value="249">Sudan (+249)</option>
                                        <option data-countryCode="SZ" value="268">Swaziland (+268)</option>
                                        <option data-countryCode="SE" value="46">Sweden (+46)</option>
                                        <option data-countryCode="CH" value="41">Switzerland (+41)</option>
                                        <!-- <option data-countryCode="SY" value="963">Syria (+963)</option> -->
                                        <option data-countryCode="TW" value="886">Taiwan (+886)</option>
                                        <option data-countryCode="TJ" value="992">Tajikistan (+992)</option>
                                        <option data-countryCode="TH" value="66">Thailand (+66)</option>
                                        <option data-countryCode="TG" value="228">Togo (+228)</option>
                                        <option data-countryCode="TO" value="676">Tonga (+676)</option>
                                        <option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
                                        <option data-countryCode="TN" value="216">Tunisia (+216)</option>
                                        <option data-countryCode="TR" value="90">Turkey (+90)</option>
                                        <option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
                                        <option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
                                        <option data-countryCode="TV" value="688">Tuvalu (+688)</option>
                                        <option data-countryCode="UG" value="256">Uganda (+256)</option>
                                        <option data-countryCode="UA" value="380">Ukraine (+380)</option>
                                        <option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
                                        <option data-countryCode="UY" value="598">Uruguay (+598)</option>
                                        <option data-countryCode="UZ" value="998">Uzbekistan (+998)</option>
                                        <option data-countryCode="VU" value="678">Vanuatu (+678)</option>
                                        <option data-countryCode="VA" value="379">Vatican City (+379)</option>
                                        <option data-countryCode="VE" value="58">Venezuela (+58)</option>
                                        <option data-countryCode="VN" value="84">Vietnam (+84)</option>
                                        <option data-countryCode="VG" value="1">Virgin Islands - British (+1)</option>
                                        <option data-countryCode="VI" value="1">Virgin Islands - US (+1)</option>
                                        <option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
                                        <option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
                                        <option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
                                        <option data-countryCode="ZM" value="260">Zambia (+260)</option>
                                        <option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
                                    </select>
                                </div>

                                <div class="col-sm-7 my-1">
                                    <input style=""  type="text" class="form-control" id="number_one" placeholder="00000000">
                                </div>
                            </div>
                        </div>

                        <div id="facebook">
                            <div class="form-group">
                                <label for="app_fb_id">Identificación de la aplicación</label>
                                <input type="text" class="form-control" id="app_fb_id" placeholder="460472458306...">
                            </div>

                            <div class="form-group">
                                <label for="app_fb_secret">Clave secreta</label>
                                <input type="text" class="form-control" id="app_fb_secret" placeholder="14108c6c5158b682832e5d2e21f...">
                            </div>

                            <div class="form-group">
                                <label for="app_fb_token">Token de la aplicación</label>
                                <input type="text" class="form-control" id="app_fb_token"  placeholder="4604724584356546776656...">
                            </div>
                        </div>

                        <div id="twitter">
                            <div class="form-group">
                                <label for="consumer_key">Clave del consumidor</label>
                                <input type="text" class="form-control" id="consumer_key" placeholder="460472458306...">
                            </div>

                            <div class="form-group">
                                <label for="consumer_secret">Clave secreta</label>
                                <input type="text" class="form-control" id="consumer_secret" placeholder="14108c6c5158b682832e5d2e21f...">
                            </div>

                            <div class="form-group">
                                <label for="token_twitter">Token de acceso</label>
                                <input type="text" class="form-control" id="token_twitter"  placeholder="4604724584356546776656...">
                            </div>

                            <div class="form-group">
                                <label for="token_secret_twitter">Token secreto de la aplicación</label>
                                <input type="text" class="form-control" id="token_secret_twitter"  placeholder="4604724584356546776656...">
                            </div>

                            <div class="form-group">
                                <label for="bearer_token">Token de portador</label>
                                <input type="text" class="form-control" id="bearer_token"  placeholder="4604724584356546776656...">
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="ModalCrear()">Guardar aplicación</button>
                </div>
            </div>
        </div>
    </div>

    {{---------------------------------- MODAL EDITAR --------------------------------------------------}}

    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar información de la aplicación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name_app_edit">Nombre</label>
                                <input type="text" class="form-control" id="name_app_edit" placeholder="Ingrese un nombre de la aplicación...">
                            </div>

                            <div class="form-group">
                                {{ Form::label('apps_edit','Seleccione la aplicación ') }}
                                {{ Form::select('apps_edit', ['Facebook' => 'Facebook', 'Twitter' => 'Twitter', 'Linkedin' => 'Linkedin'], null, ['placeholder' => 'Seleccione un aplicación...','class' => 'form-control'])}}
                            </div>

                            <div class="form-group">
                                <div class="form-row align-items-center">
                                    <div class="col-sm-12 my-1">
                                        <label for="app_fb_id">Ingrese número de teléfono</label>
                                    </div>

                                    <div class="col-sm-12 my-1">
                                        <input style=""  type="text" class="form-control" id="number_e"  placeholder="00000000">
                                    </div>

                                    <div class="col-sm-12 my-1">
                                        <input style=""  type="text" class="form-control" id="number_one_e"  placeholder="00000000">
                                    </div>

                                </div>
                            </div>

                            <div id="facebook_show">
                                <div class="form-group">
                                    <label for="app_fb_id_edit">Identificación de la aplicación</label>
                                    <input type="text" class="form-control" id="app_fb_id_edit" placeholder="460472458306...">
                                </div>

                                <div class="form-group">
                                    <label for="app_fb_secret_edit">Clave secreta</label>
                                    <input type="text" class="form-control" id="app_fb_secret_edit" placeholder="14108c6c5158b682832e5d2e21f...">
                                </div>

                                <div class="form-group">
                                    <label for="app_fb_token_edit">Token de la aplicación</label>
                                    <input type="text" class="form-control" id="app_fb_token_edit"  placeholder="4604724584356546776656...">
                                </div>
                            </div>

                            <div id="twitter_show">
                                <div class="form-group">
                                    <label for="consumer_key_edit">Clave del consumidor</label>
                                    <input type="text" class="form-control" id="consumer_key_edit" placeholder="460472458306...">
                                </div>

                                <div class="form-group">
                                    <label for="consumer_secret_edit">Clave secreta</label>
                                    <input type="text" class="form-control" id="consumer_secret_edit" placeholder="14108c6c5158b682832e5d2e21f...">
                                </div>

                                <div class="form-group">
                                    <label for="token_twitter_edit">Token de acceso</label>
                                    <input type="text" class="form-control" id="token_twitter_edit"  placeholder="4604724584356546776656...">
                                </div>

                                <div class="form-group">
                                    <label for="token_secret_twitter_edit">Token secreto de la aplicación</label>
                                    <input type="text" class="form-control" id="token_secret_twitter_edit"  placeholder="4604724584356546776656...">
                                </div>

                                <div class="form-group">
                                    <label for="bearer_token_edit">Token de portador</label>
                                    <input type="text" class="form-control" id="bearer_token_edit"  placeholder="4604724584356546776656...">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btn_edit_app" class="btn btn-primary">Editar aplicación</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        let id_change=0,
            id_changeTwitter=0,
            pageAccessToken = "";

        document.getElementById('twitter').hidden  = true;
        document.getElementById('facebook').hidden = true;

        document.getElementById('btn_edit_app').addEventListener('click',ModalEditar);

        document.getElementById("app_fb_id").addEventListener("change", Cambios);
        document.getElementById("app_fb_secret").addEventListener("change", Cambios);
        document.getElementById("app_fb_id_edit").addEventListener("change", Cambios);
        document.getElementById("app_fb_secret_edit").addEventListener("change", Cambios);

        $('select').on('change', function() {
            if(this.value == 'Facebook'){
                document.getElementById('twitter').hidden  = true;
                document.getElementById('twitter_show').hidden  = true;
                document.getElementById('facebook').hidden = false;
                document.getElementById('facebook_show').hidden = false;
            }
            if(this.value == 'Twitter'){
                document.getElementById('twitter').hidden  = false;
                document.getElementById('twitter_show').hidden  = false;
                document.getElementById('facebook').hidden = true;
                document.getElementById('facebook_show').hidden = true;
            }
            if(this.value == ''){
                document.getElementById('twitter').hidden  = true;
                document.getElementById('twitter_show').hidden  = true;
                document.getElementById('facebook').hidden = true;
                document.getElementById('facebook_show').hidden = true;
            }
        });
        function statusChangeCallback(response) {
            if (response.status === 'connected') {
            } else if (response.status === 'not_authorized') {
                window.location = "{{ route('facebook.index') }}";
            } else {
                window.location = "{{ route('facebook.index') }}";
            }
        }

        function Cambios() {
            let app_fb_id=document.getElementById('app_fb_id').value,
                app_fb_secret=document.getElementById('app_fb_secret').value,
                app_fb_id_e=document.getElementById('app_fb_id_edit').value,
                app_fb_secret_e=document.getElementById('app_fb_secret_edit').value;

            if(app_fb_id != '' && app_fb_secret != '' ){
                token(app_fb_id, app_fb_secret);
            }

            else if(app_fb_id_e != '' && app_fb_secret_e != ''){
                token(app_fb_id_e, app_fb_secret_e);
            }
        }

        function token(app_fb_id, app_fb_secret) {
            FB.api(
                '/oauth/access_token',
                'GET',
                {
                    "client_id": app_fb_id,
                    "client_secret": app_fb_secret,
                    "grant_type": "client_credentials"
                },
                function (response) {
                    if (response.access_token) {
                        pageAccessToken = response.access_token;
                        console.log(pageAccessToken);
                        document.getElementById('app_fb_token').value = pageAccessToken;
                        document.getElementById('app_fb_token_edit').value = pageAccessToken;

                    } else {
                        FB.api(
                            '/me',
                            'GET',
                            {"fields": "accounts"},
                            function (response) {
                                pageAccessToken = response.access_token;
                                document.getElementById('app_fb_token').value = pageAccessToken;
                                document.getElementById('app_fb_token_edit').value = pageAccessToken;
                            }
                        );

                    }
                }
            );
        }

        function ModalCrear() {
            if(document.getElementById('apps').value != ''){
                if(document.getElementById('apps').value == 'Facebook'){
                    let name_app      = document.getElementById('name_app').value,
                        app           = document.getElementById('apps').value;
                        app_fb_id     = document.getElementById('app_fb_id').value,
                        app_fb_secret = document.getElementById('app_fb_secret').value,
                        app_fb_token  = document.getElementById('app_fb_token').value,
                        country       = document.getElementById('country').value,
                        country_num   = document.getElementById('country_num').value,
                        number_one    = document.getElementById('number_one').value,
                        number        = document.getElementById('number').value,
                        data={name_app, app, app_fb_id, app_fb_secret, app_fb_token, number_one, number, country,country_num};
                        console.log('1' , data);

                    axios.post('{{ route('app.store') }}', data).then(response => {
                        window.location = "{{ route('app.index') }}";
                    }).catch(error=>{
                        swal('Ops', 'No es posible crear esta aplicación, revisa los datos de la página','warning');
                    });
                }
                if(document.getElementById('apps').value == 'Twitter'){
                    let name_app             = document.getElementById('name_app').value,
                        app                  = document.getElementById('apps').value,
                        consumer_key         = document.getElementById('consumer_key').value,
                        consumer_secret      = document.getElementById('consumer_secret').value,
                        token_twitter        = document.getElementById('token_twitter').value,
                        token_secret_twitter = document.getElementById('token_secret_twitter').value,
                        bearer_token         = document.getElementById('bearer_token').value,
                        country              = document.getElementById('country').value,
                        country_num          = document.getElementById('country_num').value,
                        number_one           = document.getElementById('number_one').value,
                        number               = document.getElementById('number').value,
                        data={name_app, app, consumer_key, consumer_secret, token_twitter, token_secret_twitter, bearer_token, number_one, number, country,country_num};
                    console.log(data);
                    axios.post('{{ route('app.store') }}', data).then(response => {
                        window.location = "{{ route('app.index') }}";
                    }).catch(error=>{
                        swal('Ops', 'No es posible crear esta aplicación, revisa los datos de la página','warning');
                    });
                }
            }
            else{
                swal('Error !!', 'No es posible crear esta aplicación, debe de seleccionar una aplicación','error');
            }

        }

        function show(id){
            let id_app = id ,
                data={id_app};
            axios.post("{{route('app.edit')}}",data).then( response => {
                if(response.data[0].app == "Facebook"){
                    id_change = id_app;
                    document.getElementById('twitter_show').hidden  = true;
                    document.getElementById('facebook_show').hidden = false;
                }
                else{
                    id_changeTwitter = id_app, id_app;
                    document.getElementById('twitter_show').hidden  = false;
                    document.getElementById('facebook_show').hidden = true;
                }
                document.getElementById('name_app_edit').value = response.data[0].name_app;
                document.getElementById('apps_edit').value = response.data[0].app;
                document.getElementById('app_fb_id_edit').value = response.data[0].app_fb_id;
                document.getElementById('app_fb_secret_edit').value = Base64.decode(response.data[0].app_fb_secret);
                document.getElementById('app_fb_token_edit').value = Base64.decode(response.data[0].app_fb_token);
                document.getElementById('number_one_e').value = response.data[0].number_one;
                document.getElementById('number_e').value = response.data[0].number;

                if(document.getElementById('apps_edit').value != ""){
                    $('#apps_edit option:not(:selected)').prop('disabled', true);
                    $("#apps_edit" ).attr('readonly', 'readonly');
                }
            });
        }

        function showTwitter(id){
            let id_app = id ,
                data={id_app};
            id_changeTwitter = id_app;
            axios.post("{{route('app.editTwitter')}}",data).then( response => {
                document.getElementById('twitter_show').hidden  = false;
                document.getElementById('facebook_show').hidden = true;
                document.getElementById('apps_edit').value = "Twitter"

                document.getElementById('name_app_edit').value = response.data[0].name_app;
                document.getElementById('consumer_key_edit').value = response.data[0].consumer_key;
                document.getElementById('consumer_secret_edit').value =  Base64.decode(response.data[0].consumer_secret);
                document.getElementById('token_twitter_edit').value =  Base64.decode(response.data[0].token_twitter);
                document.getElementById('bearer_token_edit').value =  Base64.decode(response.data[0].bearer_token);
                document.getElementById('number_one_e').value = response.data[0].number_one;
                document.getElementById('number_e').value = response.data[0].number;
                document.getElementById('token_secret_twitter_edit').value = Base64.decode(response.data[0].token_secret_twitter);

                if(document.getElementById('apps_edit').value != ""){
                    $('#apps_edit option:not(:selected)').prop('disabled', true);
                    $("#apps_edit" ).attr('readonly', 'readonly');
                }
            });
        }

        function ModalEditar() {
            $("#showModal").modal('toggle');
            if(document.getElementById('apps_edit').value != '') {
                if (document.getElementById('apps_edit').value == 'Facebook') {
                    let id = id_change,
                        name_app = document.getElementById('name_app_edit').value,
                        app = document.getElementById('apps_edit').value,
                        app_fb_id = document.getElementById('app_fb_id_edit').value,
                        app_fb_secret = document.getElementById('app_fb_secret_edit').value,
                        app_fb_token = document.getElementById('app_fb_token_edit').value,
                        country = document.getElementById('country').value,
                        country_num = document.getElementById('country_num').value,
                        number_one = document.getElementById('number_one_e').value,
                        number = document.getElementById('number_e').value,

                        data = {
                            id,
                            name_app,
                            app,
                            app_fb_id,
                            app_fb_secret,
                            app_fb_token,
                            number,
                            number_one,
                            country,
                            country_num
                        };

                    axios.post('{{ route('app.update') }}', data).then(response => {
                        window.location = "{{ route('app.index') }}";
                    }).catch(error => {
                        swal('Ops', 'No es posible editar esta aplicación, revisa los datos de la página', 'warning');
                    });

                }
                if (document.getElementById('apps_edit').value == 'Twitter') {
                    let id = id_changeTwitter,
                        name_app = document.getElementById('name_app_edit').value,
                        app = document.getElementById('apps_edit').value;
                        consumer_key = document.getElementById('consumer_key_edit').value;
                        consumer_secret = document.getElementById('consumer_secret_edit').value,
                        token_twitter = document.getElementById('token_twitter_edit').value,
                        token_secret_twitter = document.getElementById('token_secret_twitter_edit').value,
                        bearer_token = document.getElementById('bearer_token_edit').value,
                        country = document.getElementById('country').value,
                        country_num = document.getElementById('country_num').value,
                        number_one = document.getElementById('number_one_e').value,
                        number = document.getElementById('number_e').value,
                        data = {
                            id,
                            name_app,
                            app,
                            consumer_key,
                            consumer_secret,
                            token_twitter,
                            token_secret_twitter,
                            bearer_token,
                            number_one,
                            number,
                            country,
                            country_num
                        };
                    axios.post('{{ route('app.update') }}', data).then(response => {
                        window.location = "{{ route('app.index') }}";
                    }).catch(error => {
                        swal('Ops', 'No es posible editar esta aplicación, revisa los datos de la página', 'warning');
                    });
                }
            }
            else{
                swal('Error !!', 'No es posible crear esta aplicación, debe de seleccionar una aplicación','error');
            }
        }

        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href'); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty
            console.log(urlToRedirect); // verify if this is the right URL
            swal({
                title: "Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    // redirect with javascript here as per your logic after showing the alert using the urlToRedirect value
                    if (willDelete) {
                        swal("Exito! Se ha eliminado de forma exitosa!", {
                            icon: "success",
                        });
                        window.location.href = urlToRedirect;
                    } else {
                        swal("Cancelado!", "No se ha eliminado!", "info");
                    }
                });
        }
    </script>
@endsection
