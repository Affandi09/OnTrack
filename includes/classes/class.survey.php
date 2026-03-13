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

    public static function exists($ticket_code)
    {
        global $database;
        
        $ticket = $database->get("tickets", ["id"], ["ticket" => $ticket_code]);
        if (!$ticket) return false;

        $exists = $database->has("satisfaction_surveys", ["ticket_id" => $ticket['id']]);
        return $exists;
    }

    public static function getAll()
    {
        global $database;
        
        $surveys = $database->select("satisfaction_surveys", [
            "[>]tickets" => ["ticket_id" => "id"],
            "[>]tickets_departments" => ["department_id" => "id"]
        ], [
            "satisfaction_surveys.id",
            "satisfaction_surveys.name",
            "satisfaction_surveys.email",
            "satisfaction_surveys.q1",
            "satisfaction_surveys.q2",
            "satisfaction_surveys.q3",
            "satisfaction_surveys.q4",
            "satisfaction_surveys.q5",
            "satisfaction_surveys.timestamp",
            "tickets.id(ticket_id)",
            "tickets.ticket(ticket_code)",
            "tickets_departments.name(department_name)"
        ], [
            "ORDER" => ["satisfaction_surveys.timestamp" => "DESC"]
        ]);

        return $surveys;
    }
}
