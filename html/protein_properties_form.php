    <form method='post' action="<?php print $_SERVER["PHP_SELF"]; ?>">
         <table cellpadding=5 width=650 border=0 bgcolor=DDFFFF>
           <tr><td>
                <B>Name:</B><input type=text name='proteinName' size=50 value="<?php print $name ?>">
           </td></tr>
           <tr><td>
                <B>Sequence <?php if($seq){print "(length: $seqlen)";} ?>:</B>
           </td></tr>
           <tr><td>
               <textarea name='seq' rows='4' cols='80'><?php print $original_seq; ?></textarea>
           </td></tr>
           <tr><td>
                Select subsequence from position <input type=text name=start size=4 value="<?php if ($_POST["start"]==1){print $_POST["start"];} ?>"> to <input type=text name=end size=4 value="<?php if ($_POST["end"]==1){print $_POST["end"];} ?>"> (both included) for computation
                <hr><input type=checkbox name=composition value=1<?php if ($_POST["composition"]==1){print " checked";} ?>>Aminoacid composition
                <br><input type=checkbox name=molweight value=1<?php if ($_POST["molweight"]==1){print " checked";} ?>>Molecular weight
                <br><input type=checkbox name=abscoef value=1<?php if ($_POST["abscoef"]==1){print " checked";} ?>>Molar absorption coefficient
                <br><input type=checkbox name=charge value=1<?php if ($_POST["charge"]==1){print " checked";} ?>>Protein isoelectric point with pK values from
                        <select name=data_source>
                                <option<?php if ($data_source=="EMBOSS"){print " selected";} ?>>EMBOSS
                                <option<?php if ($data_source=="DTASelect"){print " selected";} ?>>DTASelect
                                <option<?php if ($data_source=="Solomon"){print " selected";} ?>>Solomon
                        </select>
                <br><input type=checkbox name=charge2 value=1<?php if ($_POST["charge2"]==1){print " checked";} ?>>Charge at pH = <input type=text name=pH value="<?php print $pH ?>" size=4>
                <br><input type=checkbox name=3letters value=1<?php if ($_POST["3letters"]==1){print " checked";} ?>>Show sequence as 3 letters aminoacid code
                <br><input type=checkbox name=type1 value=1<?php if ($_POST["type1"]==1){print " checked";} ?>>Show polar, non-polar and charged nature of aminoacids
                <br><input type=checkbox name=type2 value=1<?php if ($_POST["type2"]==1){print " checked";} ?>>Show polar, non-polar, Hydrofobic, and negatively or positively charged nature of aminoacids
           </td></tr>

           <tr><td align=center>
                <input type='submit' value='Submit'>
                <div align=right><a href=?info>Info</a></div>
           </td></tr>
         </table>

    </form>
