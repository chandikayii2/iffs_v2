@include('layouts.header')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>User Permission List</h4>
            </div>
            <div class="page-btn">

            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>

                                <th>ID</th>
                                <th>Permission Name</th>
                                <th>Group</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user_permissions as $user_permission)
                                <tr>

                                    <td>{{ $user_permission->id }}</td>
                                    <td>{{ $user_permission->name }}</td>
                                    <td>{{ $user_permission->group }}</td>
                                    <td>{{ $user_permission->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
</div>





</body>

</html>
