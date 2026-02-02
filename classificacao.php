<?php
require 'config.php';
$pdo = ligarBD();

$equipas = $pdo->query("SELECT id, nome FROM equipas")->fetchAll();

$class = [];
foreach ($equipas as $e) {
    $class[$e['id']] = [
        'nome'=>$e['nome'],'j'=>0,'v'=>0,'e'=>0,'d'=>0,'gm'=>0,'gs'=>0,'p'=>0
    ];
}

$res = $pdo->query("
SELECT equipa_casa, equipa_fora, golos_casa, golos_fora
FROM jogos j
JOIN resultados r ON r.id_jogo=j.id
")->fetchAll();

foreach ($res as $r) {
    $c=$r['equipa_casa']; $f=$r['equipa_fora'];
    $class[$c]['j']++; $class[$f]['j']++;
    $class[$c]['gm']+=$r['golos_casa']; $class[$c]['gs']+=$r['golos_fora'];
    $class[$f]['gm']+=$r['golos_fora']; $class[$f]['gs']+=$r['golos_casa'];

    if ($r['golos_casa']>$r['golos_fora']) {$class[$c]['v']++;$class[$c]['p']+=3;$class[$f]['d']++;}
    elseif ($r['golos_casa']<$r['golos_fora']) {$class[$f]['v']++;$class[$f]['p']+=3;$class[$c]['d']++;}
    else {$class[$c]['e']++;$class[$f]['e']++;$class[$c]['p']++;$class[$f]['p']++;}
}

usort($class,function($a,$b){
    return $b['p']<=>$a['p'] ?: (($b['gm']-$b['gs'])<=>($a['gm']-$a['gs']));
});
?>
