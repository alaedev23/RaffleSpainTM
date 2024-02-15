<?php

class AdminView extends View {
    
    public static function show($lang, $prodcuts, $raffles, $modificarDatos = false, $objSelec = null, $errors = "") {
        
        if ($errors !== "") {
            $result = self::generateSectionAdmin($prodcuts, $raffles, $modificarDatos, $objSelec);
        } else {
            $result = self::generateSectionAdmin($prodcuts, $raffles, $modificarDatos, $objSelec, $errors);
        }
        
        echo "<!DOCTYPE html><html lang=\"es\">";
        include "templates/Head.tmp.php";
        echo "<body id=\"adminPage\">";
        include "templates/Header.tmp.php";
        include "templates/Admin.tmp.php";
        include "templates/Footer.tmp.php";
        echo "</body></html>";
        
    }
    
    public static function generateSectionAdmin($products, $raffles, $modificarDatos = false, $objSelec= null, $errors = "") {
        $html = "<h1>Productos</h1>";
        
        if ($products) {
            $html .= "<form action=\"?admin/updateProducts\" method=\"post\">";
        } else {
            $html .= "<form action=\"?admin/createProducts\" method=\"post\">";
        }
        
        $html .= "<table><thead><tr>
            <th>Id</th>
            <th>Name</th>
            <th>Brand</th>
            <th>ModelCode</th>
            <th>Price</th>
            <th>Size</th>
            <th>Color</th>
            <th>Description</th>
            <th>Sex</th>
            <th>Img</th>
            <th>Quantity</th>
            <th>Discount</th>";
        
        if ($modificarDatos) {
            $html .= "<th><input class=\"btn enviar\" value=\"Update\" type=\"submit\" name=\"sendDataUpdate\"></th></tr></thead><tbody>";
        } else {
            $html .= "<th><input class=\"btn enviar\" value=\"Crear\" type=\"submit\" name=\"sendDataCrear\"></th></tr></thead><tbody>";
        }
        
        if ($objSelec !== null) {
            
        } else {
            $html .= "<tr>";
            $html .= '<td><input type="text" name="id" readonly></td>';
            $html .= '<td><input type="text" name="name"></td>';
            $html .= '<td><input type="text" name="brand"></td>';
            $html .= '<td><input type="text" name="modelcode"></td>';
            $html .= '<td><input type="text" name="price"></td>';
            $html .= '<td><input type="text" name="size"></td>';
            $html .= '<td><input type="text" name="color"></td>';
            $html .= '<td><input type="text" name="description"></td>';
            $html .= '<td><input type="text" name="sex"></td>';
            $html .= '<td><input type="file" name="img"></td>';
            $html .= '<td><input type="text" name="quantity"></td>';
            $html .= '<td><input type="text" name="discount"></td>';
            $html .= "</tr>";
        }
        
        foreach ($products as $product) {
            foreach ($products as $product) {
                $html .= "<tr>";
                $html .= "<td>$product->id</td>";
                $html .= "<td>$product->name</td>"; 
                $html .= "<td>$product->brand</td>";
                $html .= "<td>$product->modelCode</td>"; 
                $html .= "<td>$product->price</td>"; 
                $html .= "<td>$product->size</td>"; 
                $html .= "<td>$product->color</td>"; 
                $html .= "<td>$product->description</td>"; 
                $html .= "<td>$product->sex</td>"; 
                $html .= "<td>$product->img</td>"; 
                $html .= "<td>$product->quantity</td>";
                $html .= "<td>$product->discount</td>";
                $html .= "</tr>";
            }
        }
        
//         foreach ($products as $product) {
//             $html .= "<tr>";
//             $html .= "<td><input type=\"text\" name=\"id\" value=\"" . $product->id . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"name\" value=\"" . $product->name . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"brand\" value=\"" . $product->brand . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"modelCode\" value=\"" . $product->modelCode . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"price\" value=\"" . $product->price . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"size\" value=\"" . $product->size . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"color\" value=\"" . $product->color . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"description\" value=\"" . $product->description . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"sex\" value=\"" . $product->sex . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"img\" value=\"" . $product->img . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"quantity\" value=\"" . $product->quantity . "\"></td>";
//             $html .= "<td><input type=\"text\" name=\"discount\" value=\"" . $product->discount . "\"></td>";
//             $html .= "</tr>";
//         }
        
        $html .= "</tbody></table>";
        $html .= "</form>";
        
        return $html;
    }
    
}
?>