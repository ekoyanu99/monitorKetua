<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Monitor;

class Monitors extends Component
{
    public $monitors, $monitor_id;
    public $name, $type = 'web', $url, $host, $port, $db_host, $db_port = 1433, $is_active = true;
    public $isOpen = false;

    public function render()
    {
        $this->monitors = Monitor::all();
        return view('livewire.monitors')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->type = 'web';
        $this->url = '';
        $this->host = '';
        $this->port = '';
        $this->db_host = '';
        $this->db_port = 1433;
        $this->is_active = true;
        $this->monitor_id = null;
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'type' => 'required',
        ];

        if ($this->type === 'web') {
            $rules['url'] = 'required|url';
        } else {
            $rules['host'] = 'required';
        }

        $this->validate($rules);

        Monitor::updateOrCreate(['id' => $this->monitor_id], [
            'name' => $this->name,
            'type' => $this->type,
            'url' => $this->url,
            'host' => $this->host,
            'port' => $this->port,
            'db_host' => $this->db_host,
            'db_port' => $this->db_port ?? 1433,
            'is_active' => $this->is_active,
        ]);

        session()->flash(
            'message',
            $this->monitor_id ? 'Monitor Updated Successfully.' : 'Monitor Created Successfully.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $monitor = Monitor::findOrFail($id);
        $this->monitor_id = $id;
        $this->name = $monitor->name;
        $this->type = $monitor->type;
        $this->url = $monitor->url;
        $this->host = $monitor->host;
        $this->port = $monitor->port;
        $this->db_host = $monitor->db_host;
        $this->db_port = $monitor->db_port;
        $this->is_active = $monitor->is_active;

        $this->openModal();
    }

    public function delete($id)
    {

        // cek apakah monitor dengan id tersebut ada di history
        $historyCount = Monitor::find($id)->histories()->count();
        // dd($historyCount);
        if ($historyCount > 0) {
            session()->flash('message', 'Cannot delete monitor with existing history records.');
            // return;
        } else {
            Monitor::find($id)->delete();
            session()->flash('message', 'Monitor Deleted Successfully.');
        }
    }
}
