#version 300 es
// =============================================================================
// ANCHOR:SHADER_ANT_VERT
// Instance data stride = 8 floats:
//   [x, y, angle, colony_id, state, carrying, energy_norm, nn_turn]
// =============================================================================

in vec2  a_quad_pos;
in vec2  a_inst_pos;
in float a_inst_angle;
in float a_inst_colony;
in float a_inst_state;
in float a_inst_carrying;
in float a_inst_energy;    // 0-1 normalized energy
in float a_inst_nn_turn;   // -1 to 1 NN turn output

uniform mat3  u_worldToNDC;
uniform float u_ant_size;

out float v_colony;
out float v_state;
out float v_carrying;
out float v_energy;
out float v_nn_turn;
out vec2  v_local;

void main() {
    v_colony   = a_inst_colony;
    v_state    = a_inst_state;
    v_carrying = a_inst_carrying;
    v_energy   = a_inst_energy;
    v_nn_turn  = a_inst_nn_turn;
    v_local    = a_quad_pos;

    vec2 shaped = vec2(a_quad_pos.x * 1.6, a_quad_pos.y * 1.0);

    float c = cos(a_inst_angle), s = sin(a_inst_angle);
    vec2 rotated = vec2(
        c * shaped.x - s * shaped.y,
        s * shaped.x + c * shaped.y
    );

    vec2 world = a_inst_pos + rotated * u_ant_size;
    vec3 ndc   = u_worldToNDC * vec3(world, 1.0);

    gl_Position = vec4(ndc.xy, 0.0, 1.0);
}
