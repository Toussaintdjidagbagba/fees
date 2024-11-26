// importer la liste des commerciaux
        if ($request->hasFile('fichier')) {
            $ext  = $request->file('fichie')->getClientOriginalExtension();
            $error = 0; $a = 0; $error_g =0;
            $temp_error = array();
            $temp_error[$error_g]["code"] = "Veuillez reprendre avec le fichier exemplaire";
            $message_error = "";
            $tabl = "";

            if(in_array($ext,['xlsx','xls'])){
                $reference = "REF-IMPORTERCOMMERCIAUX-" . date('ymdhis');
                $namefile = $reference .'.'.$ext;
                $upload = "document/upload/";
                $request->file('fichie')->move($upload, $namefile);
                $path = $upload . $namefile;
                $tab = Excel::toArray( new ImportExcel, $path);
                $commerciaux = $tab[0];
                
                
                for ($i=1; $i < count($commerciaux); $i++) { 
                    $code = $commerciaux[$i][0];
                    /**
                     * Validation du code commercial
                     * */
                    $code_v = 0;
                    
                    if(is_int($code)){
                        // Vérifier si le code existe déjà
                        if(!isset(Commerciaux::where('codeCom', $code)->first()->codeCom))
                        {
                            $code_v = $code;
                        }
                    }else{
                        $error +=1;
                        $message_error .= "Code doit être un entier et non null; ";
                    }

                    $nom = $commerciaux[$i][1];
                    $prenom = $commerciaux[$i][2];
                    /**
                     * Validation du nom et prénom
                     * */
                    $nom_v = "";
                    $prenom_v = "";

                    if(is_string($nom) && is_string($prenom)){
                        $nom_v = $nom;
                        $prenom_v = $prenom;
                    }else{
                        $error +=1;
                        $message_error .= "Le nom et prénom doivent être des chaines de caractères et non null; ";
                    }

                    $sexe = $commerciaux[$i][3];
                    /**
                     * Validation  de sexe
                     * */
                    $sexe_v = "";
                    if(is_string($sexe)){
                        $sexe_v = $sexe;
                    }else{
                        $error +=1;
                        $message_error .= "Le sexe doit être une chaine de caractères et non null; ";
                    }

                    $tel_v = $commerciaux[$i][4];

                    $adresse_v = $commerciaux[$i][5];

                    $mail = $commerciaux[$i][6];
                    /**
                     * Validation de l'email
                     * */
                    if(is_string($mail)){
                        $mail_v = $mail;
                    }else{
                        $error +=1;
                        $message_error .= "L'email doit être une chaine de caractères et non null; ";
                    }

                    $ifu = $commerciaux[$i][7];
                    /**
                     * Validation de l'IFU
                     * */
                    $ifu_v = "";
                    if(is_string($ifu)){
                        $ifu_v = $ifu;
                    }else{
                        $ifu_v = "Non";
                    } 

                    $niveau = $commerciaux[$i][8];
                    /**
                     * Validation de l'IFU
                     * */
                    $niveau_v = "";
                    if(is_string($niveau)){
                        $niveau_v = CommerciauxController::getCodeNiveau($niveau);
                    }else{
                        $error +=1;
                        $message_error .= "Le niveau doit être une chaine de caractères et non null; ";
                    }

                    //---------------------------------------------------------------------

                    $chefEquipe = $commerciaux[$i][9];
                    $chefInspection = $commerciaux[$i][10];
                    $Inspection = $commerciaux[$i][11];
                    $Region = $commerciaux[$i][12];
                    /**
                     * Validation de l'Inspection, Equipe
                     * */
                    $chefEquipe_v = ""; $chefEquipe_v = ""; $Inspection = "";
                    $Equipe_v = ""; $Inspection_v = ""; $Region_v = "";
                    if( $niveau_v == "CONS" && (($chefEquipe==null && $chefInspection==null && $Inspection==null) || ($chefEquipe=="" && $chefInspection =="" && $Inspection =="") ) ){
                        $error +=1;
                        $message_error .= "Le code du Chef d'Equipe, le code de l'Inspecteur et l'Inspection sont obligatoire; ";
                    }
                    else
                        if ($niveau_v == "INS" && (($Inspection==null) || ( $Inspection =="") ) ) {
                            $error +=1;
                            $message_error .= "Le code de l'Inspection sont obligatoire; ";
                        }elseif ($niveau_v == "INS") {
                            // Vérification si l'inspection existe
                           if(CommerciauxController::CheckCodeInspection($Inspection) == 0){
                              // Créer l'inspection
                            if($code_v != 0)
                              $Inspection_v = CommerciauxController::createInspection($code_v, $Inspection, $Region);
                            else
                                $Inspection_v = "";
                              $Equipe_v = "";
                              $Region_v = $Region;
                           }else{
                                $error +=1;
                                $message_error .= "Le code de l'Inspection est attribué à un inspecteur déjà; ";
                           }

                        }elseif($niveau_v == "CEQP") {
                            // Vérification si le chef d'équipe existe dans une équipe existant
                           if(CommerciauxController::CheckCodeEquipe($chefEquipe) == 0){ 
                              // Créer une nouvelle équipe
                              $Inspection_v = $Inspection;
                              if($code_v != 0)
                              $Equipe_v = CommerciauxController::createEquipe($code_v, $Inspection);
                              else
                                $Equipe_v = "";
                              $Region_v = $Region;
                           }
                       }elseif($niveau_v == "CONS"){
                        if(isset(DB::table('commerciauxes')->where("codeCom", $chefEquipe)->first()->codeCom)){
                           // Vérifie si le chef d'équipe existe et récupérer le code de l'équipe
                           if(CommerciauxController::CheckCodeEquipe($chefEquipe) != 0){
                                $Equipe_v = CommerciauxController::CheckCodeEquipe($chefEquipe);
                                $Inspection_v = Hierarchie::where('codeH', $Equipe_v)->first()->superieurH;
                                //$Region_v = Hierarchie::where('codeH', $Inspection_v)->first()->superieurH;
                                $Region_v = $Region;
                           }else{
                               // Crée Equipe et récuprer le code de l'équipe
                              $Inspection_v = $Inspection;
                              $Equipe_v = CommerciauxController::createEquipe($chefEquipe, $Inspection);
                              $Region_v = $Region;
                           }
                       }else{
                            $error +=1;
                            $message_error .= "Le chef d'Equipe n'existe pas en tant que commercial; ";
                       }
                       }else{
                           if( ($chefEquipe==null) && ($chefEquipe=="") ){
                               $Equipe_v = "";
                               $Inspection_v = "";
                               $Region_v = $Region;
                           }else{
                                // Vérifie si le chef d'équipe existe et récupérer le code de l'équipe
                               if(CommerciauxController::CheckCodeEquipe($chefEquipe) != 0){
                                    $Equipe_v = CommerciauxController::CheckCodeEquipe($chefEquipe);
                                    $Inspection_v = Hierarchie::where('codeH', $Equipe_v)->first()->superieurH;
                                    $Region_v = Hierarchie::where('codeH', $Inspection_v)->first()->superieurH;
                               }else{
                                   // Crée Equipe et récuprer le code de l'équipe
                                    $Inspection_v = $Inspection;
                                    $Equipe_v = CommerciauxController::createEquipe($chefEquipe, $Inspection);
                                    $Region_v = $Region;
                               }
                           }
                       }

                    //------------------------------------------------------------------------

                    $momo = $commerciaux[$i][13];
                    /**
                     * Validation de MoMo
                     * */
                    $momo_v = "";
                    $libelle_CompteMomo_v = "";
                    if(is_int($momo) && strlen($momo) == 8 ){
                        $id_num = substr($momo, -8, 2);
                        if(CommerciauxController::verifie_mtn($id_num) == 0)
                            $libelle_CompteMomo_v = "MoMo MTN";
                        else
                            if(CommerciauxController::verifie_moov($id_num) == 0)
                                $libelle_CompteMomo_v = "MoMo MOOV";
                            else
                                $libelle_CompteMomo_v = "";

                        $momo_v =  $momo;
                    }else{
                        if ($momo == null) {
                           $momo_v = ""; 
                        }else{
                            $error +=1;
                            $message_error .= "Renseigner un numéro MOMO valide; ";
                        }
                    }

                    $num_compte_Banque = $commerciaux[$i][14]; $libelleBanque = $commerciaux[$i][15];
                    $num_compte_Banque_v = ""; $libelleBanque_v = "";
                    if(($num_compte_Banque == null || $num_compte_Banque == "") && ($libelleBanque != null || $libelleBanque != "") ) {
                        $error +=1;
                        $message_error .= "Vous avez sélectionner un libellé BANQUE et le numéro est vide; ";
                    }elseif(($num_compte_Banque != null || $num_compte_Banque != "") && ($libelleBanque == null || $libelleBanque == "")){
                        $error +=1;
                        $message_error .= "Vous avez renseigner un numéro de banque et le libellé BANQUE est vide; ";
                    }elseif(($num_compte_Banque == null || $num_compte_Banque == "") && ($libelleBanque == null || $libelleBanque == "")){
                        $num_compte_Banque_v = ""; $libelleBanque_v = "";
                    }else{
                        $num_compte_Banque_v = $num_compte_Banque; $libelleBanque_v = $libelleBanque;
                    }

                    if ($momo_v == "" && $num_compte_Banque_v == "") {
                        $error +=1;
                        $message_error .= "Renseigner un numéro sur lequel le commercial recevra ces commissions; ";
                    }
                   
                    $ifu_valeur = $commerciaux[$i][16];
                    $ifu_valeur_v = "";
                    if ($ifu_v == "Oui") {
                        if($ifu_valeur == null || $ifu_valeur == "")
                        {
                            $error +=1;
                            $message_error .= "Vous avez choisir Oui pour IFU alors renseigner le numéro de l'IFU; ";
                        }
                        else{
                            $ifu_valeur_v = $ifu_valeur;
                        }
                    }else{
                        $ifu_valeur_v = "";
                    }

                    $dateEffet = ((strlen($commerciaux[$i][17])!= 10)?
                    Fonction::ChangeFormatDate2(date('d/m/Y', ($commerciaux[$i][17] - 25569)*24*60*60)):
                    Fonction::ChangeFormatDate2($commerciaux[$i][17]));

                    $fixe = $commerciaux[$i][18];
                    $fixe_v = 0;
                    if (is_int($fixe)) {
                        $fixe_v = $fixe;
                    }else{
                        if ($fixe == "" || $fixe == null) {
                            $fixe_v = 0;
                        }else{
                            $error +=1;
                            $message_error .= "Le fixe doit être un entier; ";
                        }
                    }

                    if($error == 0){
                        if ($code_v != 0) {
                             // Enregistrer commercial
                            $add = new Commerciaux();
                            $add->codeCom = $code_v;
                            $add->nomCom = $nom_v;
                            $add->prenomCom =  $prenom_v;
                            $add->telCom = $tel_v;
                            $add->sexeCom = $sexe_v;
                            $add->adresseCom = $adresse_v;
                            $add->mail = $mail_v;
                            $add->AIB = $ifu_valeur_v;
                            $add->Niveau = $niveau_v;
                            $add->codeEquipe = $Equipe_v;
                            $add->codeInspection = $Inspection_v;
                            $add->codeRegion = $Region_v;
                            $add->action_save = 'i';
                            $add->dateEffet = $dateEffet;
                            $add->user_action = session("utilisateur")->idUser;
                            $add->save();

                            if ($momo_v != "") {
                                
                                // Créer un compte commercial
                                $addC = new Compteagent();
                                $addC->Agent = $add->id;
                                $addC->libCompte = $libelle_CompteMomo_v;
                                $addC->numCompte = $momo_v;
                                $addC->fixe = $fixe_v;
                                $addC->libCompte2 = $libelleBanque_v;
                                $addC->numCompte2 = $num_compte_Banque_v;
                                $addC->save();   
                            }elseif($num_compte_Banque_v != ""){
                                // Créer un compte commercial
                                $addC = new Compteagent();
                                $addC->Agent = $add->id;
                                $addC->libCompte2 = $libelle_CompteMomo_v;
                                $addC->numCompte2 = $momo_v;
                                $addC->fixe = $fixe_v;
                                $addC->libCompte = $libelleBanque_v;
                                $addC->numCompte = $num_compte_Banque_v;
                                $addC->save();
                            }
                         }
                    }else{
                        // Préparer le fichier des erreurs
                        $error_g += 1;
                        $temp_error[$error_g]["code"] = $commerciaux[$i][0];
                        $temp_error[$error_g]["nom"] = $commerciaux[$i][1];
                        $temp_error[$error_g]["prenom"] = $commerciaux[$i][2];
                        $temp_error[$error_g]["sexe"] = $commerciaux[$i][3];
                        $temp_error[$error_g]["tel"] = $commerciaux[$i][4];
                        $temp_error[$error_g]["adre"] = $commerciaux[$i][5];
                        $temp_error[$error_g]["email"] = $commerciaux[$i][6];
                        $temp_error[$error_g]["ifu"] = $commerciaux[$i][7];
                        $temp_error[$error_g]["niveau"] = $niveau;
                        $temp_error[$error_g]["equipe"] = $commerciaux[$i][9];
                        $temp_error[$error_g]["ins"] = $commerciaux[$i][10];
                        $temp_error[$error_g]["inspection"] = $commerciaux[$i][11];
                        $temp_error[$error_g]["region"] = $commerciaux[$i][12];
                        $temp_error[$error_g]["momo"] = $commerciaux[$i][13];
                        $temp_error[$error_g]["compte"] = $commerciaux[$i][14];
                        $temp_error[$error_g]["banque"] = $commerciaux[$i][15];
                        $temp_error[$error_g]["ifuval"] = $commerciaux[$i][16];
                        $temp_error[$error_g]["date"] = $dateEffet ;
                        $temp_error[$error_g]["Observations"] = $message_error;

                    }

                }

                if ($error_g == 0) {
                    flash("Tous les commerciaux importés avec succès.")->success();
                    return Back();
                }
                else{
                    $autre = new Collection($temp_error);
                    Session()->put('commerciauxerror', $autre);
                    flash(count($commerciaux) - 1 - $error_g.' importé(s) avec succès et '.$error_g.' error (s) trouvée(s). <br> <a href="/erreurcommerciaux"> Télécharger le fichier d\'erreur. </a>')->error();
                    return Back();
                }
            }else{
                flash("Le fichier doit être en Excel.")->error();
                return Back();
            }
        }
        else{
            flash("Aucun fichier importé.")->error();
            return Back();
        } 