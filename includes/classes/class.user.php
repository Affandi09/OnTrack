<?php

class User extends App
{

    public static function add($data)
    {
        global $database;
        $email = strtolower($data['email']);
        $count = $database->count("people", ["email" => $email]);
        if ($count == "1") {
            return "11";
        }

        $password = sha1($data['password']);
        $lastid = $database->insert("people", [
            "type" => "user",
            "roleid" => $data['roleid'],
            "clientid" => $data['clientid'],
            "name" => $data['name'],
            "email" => $email,
            "ldap_user" => $data['ldap_user'],
            "title" => $data['title'],
            "mobile" => $data['mobile'],
            "password" => $password,
            "theme" => isset($data['theme']) ? $data['theme'] : "skin-blue",
            "sidebar" => isset($data['sidebar']) ? $data['sidebar'] : "opened",
            "layout" => isset($data['layout']) ? $data['layout'] : "",
            "notes" => isset($data['notes']) ? $data['notes'] : "",
            "signature" => "",
            "sessionid" => "",
            "resetkey" => "",
            "autorefresh" => 0,
            "lang" => isset($data['lang']) ? $data['lang'] : "en",
            "ticketsnotification" => 1,
            "avatar" => "",
        ]);
        if ($lastid == "0") {
            logSystem("Failed to add user. MySQL Error: " . json_encode($database->error()));
            return "11";
        } else {
            if (isset($data['notification'])) {
                if ($data['notification'] == true)
                    Notification::newUser($lastid, $data['password']);
            }
            logSystem("User Account Added - ID: " . $lastid);
            return "10";
        }
    }

    public static function edit($data)
    {
        global $database;
        $email = strtolower($data['email']);

        if ($data['password'] == "") {
            $database->update("people", [
                "clientid" => $data['clientid'],
                "roleid" => $data['roleid'],
                "name" => $data['name'],
                "email" => $email,
                "ldap_user" => $data['ldap_user'],
                "title" => $data['title'],
                "mobile" => $data['mobile'],
                "theme" => isset($data['theme']) ? $data['theme'] : "skin-blue",
                "sidebar" => isset($data['sidebar']) ? $data['sidebar'] : "opened",
                "layout" => isset($data['layout']) ? $data['layout'] : "",
                "notes" => isset($data['notes']) ? $data['notes'] : "",
                "lang" => isset($data['lang']) ? $data['lang'] : "en",

            ], ["id" => $data['id']]);
            logSystem("User Account Edited - ID: " . $data['id']);
            return "20";
        } else {
            $password = sha1($data['password']);
            $database->update("people", [
                "clientid" => $data['clientid'],
                "roleid" => $data['roleid'],
                "name" => $data['name'],
                "email" => $email,
                "ldap_user" => $data['ldap_user'],
                "title" => $data['title'],
                "mobile" => $data['mobile'],
                "password" => $password,
                "theme" => isset($data['theme']) ? $data['theme'] : "skin-blue",
                "sidebar" => isset($data['sidebar']) ? $data['sidebar'] : "opened",
                "layout" => isset($data['layout']) ? $data['layout'] : "",
                "notes" => isset($data['notes']) ? $data['notes'] : "",
                "lang" => isset($data['lang']) ? $data['lang'] : "en",

            ], ["id" => $data['id']]);
            logSystem("User Account Edited - ID: " . $data['id']);
            return "20";
        }

    }

    public static function delete($id)
    {
        global $database;
        $database->delete("people", ["id" => $id]);
        logSystem("User Account Deleted - ID: " . $id);
        return "30";
    }

}


?>