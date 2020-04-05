<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\Request;
use App\Project;
use App\Http\Resources\Project as ProjectResource;
use App\Events\ProjectDeleted;

class ProjectController extends Controller
{

    public function index()
    {
        $projects = Project::paginate(10);
        return ProjectResource::collection($projects);
    }

    public function show(Request $request)
    {
        // return new ProjectResource($project, $request);

        $curl = curl_init();
        $id = $request->project;
        $key = config('trello.key');
        $token = config('trello.token');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.trello.com/1/batch/?urls=/boards/$id,/boards/$id/cards",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: OAuth oauth_consumer_key=\"$key\",
                oauth_token=\"$token\",
                oauth_signature_method=\"HMAC-SHA1\",
                oauth_timestamp=\"1578328909\",
                oauth_nonce=\"BOoDqaUrGkG\",
                oauth_version=\"1.0\",
                oauth_signature=\"eWiH1yrdp1OcSdCfiBZxDMSJDfw%3D\""
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        $cards = $response[1]->{200};
        $tasks = [];

        foreach ($cards as $card) {

            $labels = $card->idLabels;

            $task = (object) [
                "id" => $card->id,
                "name" => $card->name,
                "url" => $card->url,
                "due" => $card->due,
                "dueComplete" => $card->dueComplete,
                "checkItems" => $card->{'badges'}->checkItems,
                "checkItemsChecked" => $card->{'badges'}->checkItemsChecked
            ];

            if (!in_array('5e35e7b49ed4467d00375d97', $labels)) {
                array_push($tasks, $task);
            }
        }

        return [
            // 'trello_id' => $this->trello_id,
            // 'user' => $this->user,
            'id' => $response[0]->{200}->id,
            'name' => $response[0]->{200}->name,
            'url' => $response[0]->{200}->url,
            'desc' => $response[0]->{200}->desc,
            'tasks' => $tasks
            // 'cards' => $response[1]->{200}[0]->name,
        ];
    }

    public function store(StoreProjectRequest $request)
    {

        $key = config('trello.key');
        $token = config('trello.token');
        $name = rawurlencode($request->name);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.trello.com/1/boards?name=$name&idBoardSource=5e13608200faba37359184bd&idOrganization=everbrand3",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: OAuth oauth_consumer_key=\"$key\",
            oauth_token=\"$token\",
            oauth_signature_method=\"HMAC-SHA1\",oauth_timestamp=\"1578956644\",oauth_nonce=\"pCyM0RXKx4s\",oauth_version=\"1.0\",oauth_signature=\"hkvcJ%2FixJH%2FNNQZBfKXH%2FGeunS4%3D\""
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        $response->user = $request->user();
        // echo json_encode($response);

        $project = new Project;
        $project->trello_id = $response->id;
        $project->name = $response->name;
        $project->user()->associate($request->user());
        $project->save();

        return new ProjectResource($project);
    }

    public function destroy(Project $project, Request $request)
    {
        $this->authorize('destroy', $project);
        $project->delete();
        event(new ProjectDeleted($request));
        return response(null, 204);
    }
}
