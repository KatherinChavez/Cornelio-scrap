<template>
    <div class="card-body">
        <button v-if="!addMode && !editMode" class="btn btn-primary mb-3" @click="toggle">Agregar nuevo</button>
        <button v-if="addMode" class="btn btn-light mb-3" @click="toggle"><i class="fa fa-flip-horizontal fa-share"></i></button>
        <button v-if="editMode" class="btn btn-light mb-3" @click="toggle1"><i class="fa fa-flip-horizontal fa-share"></i></button>

        <table v-show="!addMode && !editMode" class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Tema</th>
                <th>Canal de Telegram</th>
                <th>Nombre canal de Telegram</th>
                <th>Números Whatsapp</th>
                <th>Palabras</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(topic, i) in topics" :key="i">
                <td>{{ topic.name }}</td>
                <td>{{ topic.channel }}</td>
                <td>{{ topic.nameTelegram }}</td>
                <td>{{ topic.whats.length }}</td>
                <td>{{ topic.words.length }}</td>
                <td> <div class="list-group-item-figure">
                    <button type="button"
                            class="btn btn-sm btn-icon btn-round btn-success mt-3"
                            @click="editTopic(topic)">
                        <i class="icon-pencil"></i>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                            @click="deleteTopic(topic)">
                        <i class="icon-close"></i>
                    </button>
                </div></td>
            </tr>
            </tbody>
        </table>
        <hr v-show="addMode || editMode">
        <div v-show="addMode || editMode" class="form mt-3">
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="topic_name">Nuevo tema</label>
                    <input type="text" class="form-control" v-model="topic_name" id="topic_name"
                           placeholder="Ingrese un nombre para el tema...">
                </div>
                <section class="card mt-4">
                    <div class="card-title mt-3 mb-3 ml-3">
                        <h6 for="whatsapp_group" class="fw-bold">Números de Whatsapp</h6>
                    </div>
                    <div class="list-group list-group-messages list-group-flush" id="whatsapp_group">
                        <div v-if="whatsapp_numbers.length > 0 && addMode" v-for="(whatsapp, i) in whatsapp_numbers"
                             :key="i"
                             class="list-group-item unread text-center">
                            <div class="list-group-item-body">
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <h6 class="list-group-item-title">
                                            {{ whatsapp.codigo }}
                                        </h6>
                                    </div>
                                    <div class="col-sm-5">
                                        <h6 class="list-group-item-title">
                                            {{ whatsapp.id }}
                                        </h6>
                                    </div>
                                    <div class="col-sm-5">
                                        <h6 class="list-group-item-title">
                                            {{ whatsapp.name }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item-figure">
                                <button type="button" class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                                        @click="removeWhatsapp(i)">
                                    <i class="icon-close"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="whatsapp_numbers.length > 0 && editMode"
                             v-for="(whatsapp, index) in whatsapp_numbers"
                             :key="index"
                             class="list-group-item unread text-center">
                            <div class="list-group-item-body">
                                <div class="row form-group">
                                    <div class="col-sm-2">
                                        <input class="form-control" :value="whatsapp.codigo">
                                    </div>
                                    <div class="col-sm-5">
                                        <input class="form-control" v-model="whatsapp_numbers[index].numeroTelefono">
                                    </div>
                                    <div class="col-sm-5">
                                        <input class="form-control" v-model="whatsapp_numbers[index].descripcion">
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item-figure">
                                <button type="button" class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                                        @click="removeWhatsapp(index, whatsapp)">
                                    <i class="icon-close"></i>
                                </button>
                            </div>
                        </div>
                        <div class="list-group-item unread">
                            <div class="list-group-item-body pl-1 pl-md-1">
                                <div class="row form-group">
                                    <div class="select2-input col-sm-2">

                                        <select class="form-control" v-model="country">
                                            <option data-countryCode="US" value="1" selected>USA (+1)</option>
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
                                    <div class="col-sm-5 input-sm">
                                        <input class="form-control" type="number"
                                               placeholder="Ingrese el número del Whatsapp..."
                                               v-model="whatsapp_id"/>
                                    </div>
                                    <div class="col-sm-5 input-sm">
                                        <input class="form-control" type="text"
                                               placeholder="Ingrese el nombre del Whatsapp..."
                                               v-model="whatsapp_name"/>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item-figure">
                                <button v-if="editMode" type="button"
                                        class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                        @click="addNewWhatsapp">
                                    <i class="icon-plus"></i>
                                </button>
                                <button v-if="addMode" type="button"
                                        class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                        @click="addWhatsapp">
                                    <i class="icon-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="card mt-4">
                    <div class="card-title mt-3 mb-3 ml-3">
                        <h6 for="telegram_group" class="fw-bold">Canales de Telegram</h6>
                        <p>Para recibir notificaciones deberás ingresar al canal de Telegram el bot "CornelioMonitoreo" como administrador,
                        dicho canal ingresado permitira enviar información acerca los temas deseados</p>
                    </div>
                    <div class="list-group list-group-messages list-group-flush" id="telegram_group">
                        <div class="list-group-item unread">
                            <div class="list-group-item-body pl-1 pl-md-2">
                                <div class="row form-group">
                                    <div class="col-sm-6">
                                        <input class="form-control" type="number" @change="Telegram"
                                               placeholder="1464149456" v-model="telegram_id"/>
                                    </div>
                                    <div class="col-sm-6">
                                        <input class="form-control" type="text" @change="Telegram"
                                               placeholder="Nombre del canal..."
                                               v-model="telegram_name"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="card mt-4">
                    <div class="card-title mt-3 mb-3 ml-3">
                        <h6 for="telegram_group" class="fw-bold">Palabras</h6>
                    </div>
                    <div class="list-group list-group-messages list-group-flush" id="words">
                        <div v-if="words.length > 0 && addMode" v-for="(word, i) in words" :key="i"
                             class="list-group-item unread text-center">
                            <div class="list-group-item-body">
                                <div class="col-sm-12">
                                    <div class="row form-group">
                                        <div class="col-sm-6">
                                            <h6 class="list-group-item-title">
                                                {{ word.word }}
                                            </h6>
                                        </div>
                                        <div class="select2-input col-sm-6">
                                            <h6 v-if="word.priority == '1'">Alta</h6>
                                            <h6 v-else-if="word.priority == '2'">Media</h6>
                                            <h6 v-else>Baja</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item-figure">
                                <button type="button" class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                                        @click="removeWord(i)">
                                    <i class="icon-close"></i>
                                </button>
                            </div>
                        </div>
                        <div v-if="words.length > 0 && editMode" v-for="(word, index) in words" :key="index"
                             class="list-group-item unread text-center">
                            <div class="list-group-item-body">
                                <div class="col-sm-12">
                                    <div class="row form-group">
                                        <div class="col-sm-6">
                                            <h6 class="list-group-item-title">
                                                {{ word.palabra }}
                                            </h6>
                                        </div>
                                        <div class="select2-input col-sm-6">
                                            <h6 v-if="word.priority == '1'">Alta</h6>
                                            <h6 v-else-if="word.priority == '2'">Media</h6>
                                            <h6 v-else-if="word.priority == '3'">Baja</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item-figure">
                                <button type="button" class="btn btn-sm btn-icon btn-round btn-danger mt-3"
                                        @click="removeWord(index, word)">
                                    <i class="icon-close"></i>
                                </button>
                            </div>
                        </div>
                        <div class="list-group-item unread">
                            <div class="list-group-item-body pl-1 pl-md-2">
                                <div class="col-sm-12 form-group">
                                    <div class="row form-group">
                                        <div class="col-sm-6">
                                            <input class="form-control" type="text" placeholder="Ingrese una palabra..."
                                                   v-model="word"/>
                                        </div>
                                        <div class="select2-input col-sm-6">
                                            <select class="form-control" v-model="priority">
                                                <option v-for="priority in priorities" :value="priority.value">
                                                    {{ priority.priority }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item-figure">
                                <button v-if="addMode" type="button"
                                        class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                        @click="addWords">
                                    <i class="icon-plus"></i>
                                </button>
                                <button v-if="editMode" type="button"
                                        class="btn btn-sm btn-icon btn-round btn-success mt-3"
                                        @click="addNewWords">
                                    <i class="icon-plus"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </section>
                <div v-if="addMode" class="form-control form-group">
                    <button class="btn btn-sm btn-block btn-primary" @click="storeTopic">Guardar</button>
                </div>
                <div v-if="editMode" class="form-control form-group">
                    <button class="btn btn-sm btn-block btn-primary" @click="modifyTopic">Modificar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TopicComponent",
    data() {
        return {
            addMode: false,
            editMode: false,
            topic_name: '',
            telegram_name: '',
            telegram_id: '',
            whatsapp_name: '',
            whatsapp_id: '',
            country: '',
            current_topic: '',
            word: '',
            words: [],
            whatsapp_numbers: [],
            priority: 3,
            priorities: [
                {value: 1, priority: 'Alta'},
                {value: 2, priority: 'Media'},
                {value: 3, priority: 'Baja'},
            ],
            code:'',
            codes: [],
            topics: []
        }
    },
    methods: {
        toggle() {
            (this.addMode) ? this.addMode = false : this.addMode = true;
            (this.editMode) ? this.editMode = false : this.editMode = false;
            this.clear()
        },
        toggle1() {
            (this.editMode) ? this.editMode = false : this.editMode = false;
            this.clear()
        },
        clear() {
            this.whatsapp_numbers = []
            this.words = []
            this.topic_name = ''
            this.telegram_name = ''
            this.telegram_id = ''
            this.whatsapp_name = ''
            this.whatsapp_id = ''
            this.country = ''
            this.word = ''
            this.priority = ''
            this.country = ''
        },
        syncTopics() {
            axios.get('/topics/syncTopics').then(response => {
                this.topics = response.data
            })
        },
        addNewWhatsapp() {
            if (this.whatsapp_id && this.whatsapp_name && this.country) {
                this.whatsapp_numbers.push({codigo:this.country, numeroTelefono: this.whatsapp_id, descripcion: this.whatsapp_name})
                this.country = ''
                this.whatsapp_id = ''
                this.whatsapp_name = ''
            } else {
                Swal.fire({
                    title: "Oops",
                    text: 'Complete los espacios correctamente',
                    icon: 'warning'
                })
            }
        },
        addWhatsapp() {
            if (this.whatsapp_id && this.whatsapp_name && this.country) {
                this.whatsapp_numbers.push({codigo:this.country,id: this.whatsapp_id, name: this.whatsapp_name})
                this.country = ''
                this.whatsapp_id = ''
                this.whatsapp_name = ''
            } else {
                Swal.fire({
                    title: "Oops",
                    text: 'Complete los espacios correctamente',
                    icon: 'warning'
                })
            }
        },
        removeWhatsapp(i, whatsapp) {
            axios.post('/topics/delete', whatsapp).then(response => {
                this.whatsapp_numbers.splice(i, 1)
            })
        },
        addWords() {
            if (this.word && this.priority) {
                this.words.push({word: this.word, priority: this.priority})
                this.word = ''
                this.priority = ''
            } else {
                Swal.fire({
                    title: "Oops",
                    text: 'Complete los espacios correctamente',
                    icon: 'warning'
                })
            }
        },
        addNewWords() {
            if (this.word && this.priority) {
                this.words.push({palabra: this.word, priority: this.priority})
                this.word = ''
                this.priority = ''
            } else {
                Swal.fire({
                    title: "Oops",
                    text: 'Complete los espacios correctamente',
                    icon: 'warning'
                })
            }
        },
        removeWord(i, word) {
            axios.post('/topics/deleteWord', word).then(response => {
                if(response.status===201){
                    swal('Opss', response.data, 'warning');
                    return false;
                }
                this.words.splice(i, 1)
            })
        },
        storeTopic() {
            if (this.topic_name && this.whatsapp_numbers.length > 0 && this.telegram_name && this.telegram_id && this.words.length > 0) {
                const data = new FormData(),
                    topic_name = this.topic_name,
                    whatsapps = JSON.stringify(this.whatsapp_numbers),
                    telegramName = this.telegram_name,
                    telegramChannel = this.telegram_id,
                    words = JSON.stringify(this.words)
                data.append('topic_name', topic_name)
                data.append('whatsapps', whatsapps)
                data.append('telegramName', telegramName)
                data.append('telegramChannel', telegramChannel)
                data.append('words', words)
                axios.post('/topics/store', data).then(response => {
                    this.addMode = false
                    this.clear()
                    this.syncTopics()
                    swal('Agregado', 'Se guardó exitosamente', 'success')
                }).catch(() => {
                })
            } else {
                swal('Error', 'Complete todos los espacios, por favor', 'error')
            }
        },
        modifyTopic() {
            if (this.topic_name && this.whatsapp_numbers.length > 0 && this.telegram_name && this.telegram_id && this.words.length > 0) {
                const data = new FormData(),
                    topic_id = this.current_topic,
                    topic_name = this.topic_name,
                    whatsapps = JSON.stringify(this.whatsapp_numbers),
                    telegramName = this.telegram_name,
                    telegramChannel = this.telegram_id,
                    words = JSON.stringify(this.words)
                data.append('topic_id', topic_id)
                data.append('topic_name', topic_name)
                data.append('whatsapps', whatsapps)
                data.append('telegramName', telegramName)
                data.append('telegramChannel', telegramChannel)
                data.append('words', words)
                axios.post('/topics/update', data).then(response => {
                    this.editMode = false
                    this.clear()
                    this.syncTopics()
                    swal('Modificado', 'Se modificó exitosamente', 'success')
                }).catch(() => {
                    console.log("Error.....")
                })
            } else {
                swal('Error', 'Complete todos los espacios, por favor', 'error')
            }
        },
        editTopic(topic) {
            console.log(topic);
            (this.addMode) ? this.addMode = false : this.addMode = false;
            this.editMode = true
            this.topic_name = topic.name
            this.telegram_id = topic.channel
            this.telegram_name = topic.nameTelegram
            this.whatsapp_numbers = topic.whats
            this.words = topic.words
            this.current_topic = topic.id
        },
        deleteTopic(content) {
            swal({
                title: "¿Esta seguro?",
                text: "una vez eliminado el contenido no podrá recuperarlo",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then( response=> {
                if (response) {
                    axios.get(`/topics/deleteTopic/${content.id}`).then(response => {
                        if(response.status===201){
                            swal('Opss', response.data, 'warning');
                            return false;
                        }
                        this.editMode = false
                        this.clear()
                        this.syncTopics()
                        swal('Eliminado', 'Se eliminó exitosamente', 'success')
                    }).catch((error) => {
                        console.log("Error.....".error)
                    })
                }
            });

        },
        Telegram(){
            swal({
                title: "Instrucciones !",
                text: "Sigue los siguientes pasos:" + "\n" +
                "1. Crea el canal en Telegram" + "\n" +
                "2 .Agrega el bot CornelioMonitoreo a tu canal como administrador " + "\n" +
                "3 .Envia el siguiente mensaje 'bot ingresado' en el canal" + "\n" +
                "4 .Ingresa el id del canal y el nombre del canal",
            });
        }
    },
    beforeMount() {
        this.syncTopics()
    }
}
</script>

<style scoped>

</style>
