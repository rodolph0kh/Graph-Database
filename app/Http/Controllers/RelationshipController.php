<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\HandleStoreRelationshipRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    public function store(HandleStoreRelationshipRequest $handleStoreRelationshipRequest)
    {
        $relationshipType = 'relationship_' . $handleStoreRelationshipRequest->get('type');

        $table = DB::table('relationship_types')->where('type', $relationshipType)->first();
        
        if (!$table) {
            $tableName = $relationshipType;
            
            DB::table('relationship_types')->insert([
                'type' => $tableName,
            ]);

            Artisan::call('relationship:table', [
                'name' => $tableName,
            ]);
        } else {
            $tableName = $table->type; 
        }

        $source = $handleStoreRelationshipRequest->get('source');
        $destination = $handleStoreRelationshipRequest->get('destination');
        
        $sourceType = 'node_' . $source['type'];
        $destinationType = 'node_' . $destination['type'];

        $sourceTypeExists = DB::table('node_types')->where('type', $sourceType)->first();
        $destinationTypeExists = DB::table('node_types')->where('type', $destinationType)->first();

        if (!$sourceTypeExists || !$destinationTypeExists) {
            throw new Exception('The source or destination node type does not exists.');
        }

        $sourceNode = DB::table($sourceType)->where('name', $source['name'])->first();
        $destinationNode = DB::table($destinationType)->where('name', $destination['name'])->first();

        if (!$sourceNode || !$destinationNode) {
            throw new Exception('The source or destination node does not exists.');
        }

        $sourceId = $sourceNode->id;
        $destinationId = $destinationNode->id;

        return DB::table($tableName)->insert([
            'directed' => $handleStoreRelationshipRequest->get('directed'),
            'source_id' => $sourceId,
            'source_type' => $sourceType,
            'destination_id' => $destinationId,
            'destination_type' => $destinationType,
            'properties' => json_encode($handleStoreRelationshipRequest->get('properties'))
        ]);
    }

    public function retreiveRelationshipByType(Request $request)
    {
        $table = DB::table('relationship_types')->where('type', 'relationship_' . $request->type)->first();

        if (!$table) {
            throw new Exception('The relationship type does not exist');
        }

        return DB::table($table->type)->get();
    }
}
