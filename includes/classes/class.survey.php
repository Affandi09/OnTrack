<?php

class Survey extends App
{
    public static function add($data)
    {
        global $database;

        // Map ticket code to ticket ID if necessary
        $ticket_id = 0;
        if (isset($data['ticket_id'])) {
            $ticket = $database->get("tickets", ["id"], ["ticket" => $data['ticket_id']]);
            if ($ticket) {
                $ticket_id = $ticket['id'];
            }
        }

        $lastid = $database->insert("satisfaction_surveys", [
            "ticket_id" => $ticket_id,
            "name" => $data['nama'],
            "department_id" => $data['department_id'],
            "email" => $data['email'],
            "q1" => $data['q1'],
            "q2" => $data['q2'],
            "q3" => $data['q3'],
            "q4" => $data['q4'],
            "q5" => $data['q5'],
            "timestamp" => date('Y-m-d H:i:s')
        ]);

        if ($lastid == "0") {
            return "11"; // Error status code
        } else {
            logSystem("Satisfaction Survey Added - Ticket ID: " . $ticket_id);
            return "10"; // Success status code
        }
    }
}
