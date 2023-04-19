<?php

namespace App\Http\Controllers;

use App\Http\Requests\HandleStoreNodeRequest;
use Exception;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class NodeController extends Controller
{
    public function store(HandleStoreNodeRequest $handleStoreNodeRequest)
    {
        $nodeType = 'node_' . $handleStoreNodeRequest->get('type');

        $table = DB::table('node_types')->where('type', $nodeType)->first();
        
        if (!$table) {
            $tableName = $nodeType;
            
            DB::table('node_types')->insert([
                'type' => $tableName,
            ]);

            Artisan::call('node:table', [
                'name' => $tableName,
            ]);
        } else {
            $tableName = $table->type; 
        }

        $nodeExists = DB::table($tableName)->where('name', $handleStoreNodeRequest->get('name'))->first();

        if ($nodeExists) {
            throw new Exception('The node name is already taken for this type. Please choose a dif ferent name.');
        }
        
        return DB::table($tableName)->insert([
            'name' => $handleStoreNodeRequest->get('name'),
            'properties' =>  json_encode($handleStoreNodeRequest->get('properties'))
        ]);
    }

    public function retreiveNodesByType(Request $request)
    {
        $table = DB::table('node_types')->where('type', 'node_' . $request->type)->first();

        if (!$table) {
            throw new Exception('The node type does not exist');
        }

        return DB::table($table->type)->get();
    }

    public function retreiveNode(Request $request)
    {
        $table = DB::table('node_types')->where('type', 'node_' . $request->type)->first();

        if (!$table) {
            throw new Exception('The node type does not exist');
        }

        $node = DB::table($table->type)->where('name', $request->name)->first();

        if (!$node) {
            throw new Exception('This node does not exist');
        }

        return $node;
    }

    public function getNodeRelationshipsByType(Request $request)
    {
        if ($request->direction == 'source') {
            $secondNodeDirection = 'destination';
        } elseif ($request->direction == 'destination') {
            $secondNodeDirection = 'source';
        } else {
            throw new Exception('You should specify the relationship direction');
        }

        $nodeTable = DB::table('node_types')->where('type', 'node_' . $request->nodeType)->first();

        if (!$nodeTable) {
            throw new Exception('The node type does not exist');
        }

        $node = DB::table($nodeTable->type)->where('name', $request->name)->first();

        if (!$node) {
            throw new Exception('This node does not exist');
        }

        $relationshipTable = DB::table('relationship_types')->where('type', 'relationship_' . $request->relationshipType)->first();

        if (!$relationshipTable) {
            throw new Exception('The relationship type does not exist');
        }

        $relationships = DB::table($relationshipTable->type)->where($request->direction . '_id', $node->id)
                                                ->where($request->direction . '_type', $nodeTable->type)
                                                ->get();

        $nodes = collect();
        
        foreach($relationships as $relationship) {
            $secondNodeId = $relationship->{$secondNodeDirection . '_id'};
            $secondNodeType = $relationship->{$secondNodeDirection . '_type'};

            $node = DB::table($secondNodeType)->where('id', $secondNodeId)->first();

            $nodes->push($node);
        }

        return $nodes;
    }
}
